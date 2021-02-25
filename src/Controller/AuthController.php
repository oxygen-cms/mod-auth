<?php

namespace OxygenModule\Auth\Controller;

use DarkGhostHunter\Laraguard\Http\Controllers\Confirms2FACode;
use GuzzleHttp\Client;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Foundation\Application;
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
use Oxygen\Auth\Entity\AuthenticationLogEntry;
use Oxygen\Auth\Entity\User;
use Oxygen\Auth\Repository\AuthenticationLogEntryRepositoryInterface;
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

    const AUTHENTICATION_LOG_PER_PAGE = 10;

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
     * Show the login form.
     *
     * @param Request $request
     * @return View
     */
    public function getLogin(Request $request) {
        if($request->has('intended')) {
            $request->session()->put('url.intended', $request->get('intended'));
        }
        return view('oxygen/mod-auth::login');
    }

    /**
     * Required for throttling login attempts.
     *
     * @return string
     */
    protected function username() {
        // TODO: get the current username
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
        $remember = $request->input('remember') === '1';

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
        return $request->session()->pull('url.intended', $preferences->get('modules.auth::dashboard'));
    }

    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View
     */
    public function getTwoFactorAuthNotice() {
        return redirect(route('auth.getPrepareTwoFactor'));
    }

    /**
     * Begins to set-up two-factor authentication for this user.
     *
     * @param Request $request
     * @return Application|\Illuminate\Contracts\View\Factory|View
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDetailsApi() {
        $user = auth()->user();

        return response()->json([
            'user' => $user->toArray(),
            'status' => Notification::SUCCESS
        ]);
    }

    /**
     * Get entries from the login log.
     *
     * @param AuthenticationLogEntryRepositoryInterface $entries
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticationLogEntries(AuthenticationLogEntryRepositoryInterface $entries) {
        $paginator = $entries->findByUser(auth()->user(), self::AUTHENTICATION_LOG_PER_PAGE, null);

        return response()->json([
            'items' => array_map(function(AuthenticationLogEntry $e) { return $e->toArray(); }, $paginator->items()),
            'totalItems' => $paginator->total(),
            'itemsPerPage' => $paginator->perPage(),
            'status' => Notification::SUCCESS
        ]);
    }

    /**
     * Returns filled in IP geolocation data from a geolocation service.
     * @param string $ip
     * @return Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|Response
     */
    public function getIPGeolocation($ip) {
        $client = new Client();

        try {
            $res = $client->request('GET', config('oxygen.auth.ipGeolocationUrl'), [
                'query' => ['apiKey' => config('oxygen.auth.ipGeolocationKey'), 'ip' => $ip]
            ]);
            return response($res->getBody());
        } catch(\GuzzleHttp\Exception\ClientException $e) {
            report($e);
            return response()->json([
                'content' => 'IP geolocation failed',
                'status' => Notification::FAILED
            ]);
        }
    }

    /**
     * Change the user's password.
     *
     * @param AuthManager $auth
     * @param Request $request
     * @param Factory $validationFactory
     * @return \Illuminate\Http\JsonResponse
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

            return response()->json([
                'content' => __('oxygen/mod-auth::messages.password.changed'),
                'status' => Notification::SUCCESS
            ]);
        } else {
            return response()->json([
                'content' => $validator->messages()->first(),
                'status' => Notification::FAILED
            ]);
        }
    }

    /**
     * Deletes the user permanently.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteForce(AuthManager $auth) {
        $user = $auth->guard()->user();
        $this->repository->delete($user);

        return response()->json([
            'content' => __('oxygen/mod-auth::messages.account.terminated'),
            'status' => Notification::SUCCESS
        ]);
    }

}
