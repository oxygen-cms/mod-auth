<?php

namespace OxygenModule\Auth\Controller;

use DarkGhostHunter\Laraguard\Http\Controllers\Confirms2FACode;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\SessionManager;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Oxygen\Auth\Entity\User;
use Oxygen\Auth\Repository\UserRepositoryInterface;
use Oxygen\Core\Blueprint\BlueprintNotFoundException;
use Oxygen\Core\Contracts\Routing\ResponseFactory;
use Oxygen\Preferences\PreferenceNotFoundException;
use Oxygen\Preferences\PreferencesManager;
use OxygenModule\Auth\Fields\UserFieldSet;
use Oxygen\Crud\Controller\BasicCrudController;
use Oxygen\Core\Http\Notification;
use Oxygen\Core\Blueprint\BlueprintManager;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthController extends BasicCrudController {

    use Confirms2FACode;
    use ThrottlesLogins;

    /**
     * Constructs the AuthController.
     *
     * @param UserRepositoryInterface $repository
     * @param BlueprintManager $manager
     * @param UserFieldSet $fieldSet
     * @throws BlueprintNotFoundException
     */
    public function __construct(UserRepositoryInterface $repository, BlueprintManager $manager, UserFieldSet $fieldSet) {
        parent::__construct($repository, $manager->get('Auth'), $fieldSet);
    }

    /**
     * If logged in, redirect to the dashboard.
     * If not, show the login page.
     *
     * @param AuthManager $auth
     * @param UrlGenerator $url
     * @param ResponseFactory $response
     * @param PreferencesManager $preferences
     * @return RedirectResponse
     * @throws PreferenceNotFoundException
     */
    public function getCheck(AuthManager $auth, UrlGenerator $url, ResponseFactory $response, PreferencesManager $preferences) {
        if($auth->guard()->check()) {
            return $response->redirectToIntended($url->route($preferences->get('modules.auth::dashboard')));
        } else {
            return $response->redirectGuest($url->route('auth.getLogin'));
        }
    }

    /**
     * Show the login form.
     *
     * @return View
     */
    public function getLogin() {
        return view('oxygen/mod-auth::login', [
            'title' => __('oxygen/mod-auth::ui.login.title')
        ]);
    }

    /**
     * Required for throttling login attempts.
     *
     * @return string
     */
    protected function username() {
        return 'username';
    }

    /**
     * Login action.
     *
     * @param Request $request
     * @param AuthManager $auth
     * @param Dispatcher $events
     * @param PreferencesManager $preferences
     * @return Response
     * @throws PreferenceNotFoundException
     * @throws ValidationException
     */
    public function postLogin(Request $request, AuthManager $auth, Dispatcher $events, PreferencesManager $preferences) {
        $remember = $request->input('remember') === '1' ? true : false;

        try {
            if($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                $this->sendLockoutResponse($request);
            }
            
            if ($auth->guard()->attempt([
                'username' => $request->input('username'),
                'password' => $request->input('password')
            ], $remember)) {
                $events->dispatch('auth.login.successful', [$auth->guard()->user()]);

                $user = $auth->guard()->user();
                assert($user instanceof User);
                return notify(
                    new Notification(
                        __('oxygen/mod-auth::messages.login.successful', ['name' => $user->getFullName()])
                    ),
                    ['redirect' => $this->getPostLoginRedirectPath($request, $preferences), 'hardRedirect' => true]
                );
            } else {
                $this->incrementLoginAttempts($request);
                
                $events->dispatch('auth.login.failed', [$request->input('username')]);

                return notify(
                    new Notification(
                        __('oxygen/mod-auth::messages.login.failed'),
                        Notification::FAILED
                    )
                );
            }
        } catch(HttpResponseException $exception) {
            $this->incrementLoginAttempts($request);
            throw $exception;
        }
    }

    /**
     * @param Request $request
     * @param PreferencesManager $preferences
     * @return mixed
     * @throws PreferenceNotFoundException
     */
    protected function getPostLoginRedirectPath(Request $request, PreferencesManager $preferences) {
        $path = $request->session()->pull('url.intended', $preferences->get('modules.auth::dashboard'));
        return $path;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function getTwoFactorAuthNotice() {
        return redirect(route('auth.getPrepareTwoFactor'));
    }

    /**
     * Begins to set-up two-factor authentication for this user.
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function getPrepareTwoFactor(Request $request) {
        $secret = $request->user()->createTwoFactorAuth();

        return view('oxygen/mod-auth::prepareTwoFactor', [
            'as_qr_code' => $secret->toQr(),     // As QR Code
            'as_uri'     => $secret->toUri(),    // As "otpauth://" URI.
            'as_string'  => $secret->toString(), // As a string
        ]);
    }

    public function postConfirmTwoFactor(Request $request, PreferencesManager $preferences) {
        $code = str_replace(' ', '', $request->input('2fa_code'));
        $activated = $request->user()->confirmTwoFactorAuth($code);
        
        if(!$activated) {
            return notify(
                new Notification(
                    __('oxygen/mod-auth::messages.twoFactor.failure'),
                    Notification::FAILED
                )
            );
        } else {
            return notify(
                new Notification(
                    __('oxygen/mod-auth::messages.twoFactor.success')
                ),
                ['redirect' => $this->getPostLoginRedirectPath($request, $preferences), 'hardRedirect' => true ]
            );
        }
    }

    /**
     * Log the user out.
     * 
     * @param AuthManager $auth
     * @param Dispatcher $events
     * @return mixed
     */
    public function postLogout(AuthManager $auth, SessionManager $session, Dispatcher $events) {
        $user = $auth->guard()->user();

        $auth->guard()->logout();
        // NOTE: flushing session on logout appears to fix a subtle bug where
        // logging out from one user, then attempting to login again,
        // would error out and cause a HTTP 403 error to be returned (when using two factor auth)
        // see: Illuminate\Session\Middleware\AuthenticateSession line 55
        $session->flush();

        $events->dispatch('auth.logout.successful', [$user]);

        return notify(
            new Notification(__('oxygen/mod-auth::messages.logout.successful')),
            ['redirect' => 'auth.getLogoutSuccess', 'hardRedirect' => true] // redirect without SmoothState
        );
    }

    /**
     * Show the logout success message.
     *
     * @return View
     */
    public function getLogoutSuccess() {
        return view('oxygen/mod-auth::logout', [
            'title' => __('oxygen/mod-auth::ui.logout.title')
        ]);
    }

    /**
     * Show the current user's profile.
     *
     * @param null $user
     * @return View
     */
    public function getInfo($user = null) {
        $user = auth()->user();

        return view('oxygen/mod-auth::profile', [
            'user' => $user,
            'fields' => $this->crudFields,
            'title' => __('oxygen/mod-auth::ui.profile.title')
        ]);
    }

    /**
     * Shows the update form.
     *
     * @param null $user
     * @return View
     */
    public function getUpdate($user = null) {
        $user = auth()->user();

        return view('oxygen/mod-auth::update', [
            'user' => $user,
            'fields' => $this->crudFields,
            'title' => __('oxygen/mod-auth::ui.update.title')
        ]);
    }

    /**
     * Updates a the user.
     *
     * @param Request $request
     * @param null $user
     * @return Response
     * @throws \Exception
     */
    public function putUpdate(Request $request, $user = null) {
        $user = auth()->user();

        return parent::putUpdate($request, $user);
    }

    /**
     * Redirects the user to the preferences.
     *
     * @return RedirectResponse
     */
    public function getPreferences(ResponseFactory $response) {
        return $response->redirectToRoute('preferences.getView', ['user']);
    }

    /**
     * Change password form.
     *
     * @return View
     */
    public function getChangePassword(AuthManager $auth) {
        $user = $auth->guard()->user();

        return view('oxygen/mod-auth::changePassword', [
            'user' => $user,
            'title' => __('oxygen/mod-auth::ui.changePassword.title')
        ]);
    }

    /**
     * Change the user's password.
     *
     * @param AuthManager $auth
     * @param Request $request
     * @param Factory $validationFactory
     * @return Response
     * @throws \Oxygen\Data\Exception\InvalidEntityException
     */
    public function postChangePassword(AuthManager $auth, Request $request, Factory $validationFactory) {
        $user = $auth->guard()->user();
        $input = $request->all();

        $validator = $validationFactory->make(
            $input,
            [
                'oldPassword' => ['required', 'hashes_to:' . $user->getPassword()],
                'password' => ['required', 'same:passwordConfirmation'],
                'passwordConfirmation' => ['required']
            ]
        );

        if($validator->passes()) {
            $user->setPassword($input['password']);
            $this->repository->persist($user);

            return notify(
                new Notification(__('oxygen/mod-auth::messages.password.changed')),
                ['redirect' => $this->blueprint->getRouteName('getInfo')]
            );
        } else {
            return notify(
                new Notification($validator->messages()->first(), Notification::FAILED)
            );
        }
    }

    /**
     * Deletes the user permanently.
     *
     * @return Response
     */
    public function deleteForce(AuthManager $auth) {
        $user = $auth->guard()->user();
        $this->repository->delete($user);

        return notify(
            new Notification(__('oxygen/mod-auth::messages.account.terminated')),
            ['redirect' => $this->blueprint->getRouteName('getLogin'), 'hardRedirect' => true]
        );
    }

}