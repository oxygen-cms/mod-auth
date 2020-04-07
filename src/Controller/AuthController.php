<?php

namespace OxygenModule\Auth\Controller;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Store;
use Illuminate\Validation\Factory;
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

class AuthController extends BasicCrudController {

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
     * @return RedirectResponse
     * @throws PreferenceNotFoundException
     */
    public function getCheck(Guard $auth, UrlGenerator $url, ResponseFactory $response, PreferencesManager $preferences) {
        if($auth->check()) {
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
     * Login action.
     *
     * @param Request $request
     * @param StatefulGuard $auth
     * @param Dispatcher $events
     * @param Store $session
     * @param PreferencesManager $preferences
     * @return Response
     * @throws PreferenceNotFoundException
     */
    public function postLogin(Request $request, StatefulGuard $auth, Dispatcher $events, Store $session, PreferencesManager $preferences) {
        $remember = $request->input('remember') === '1' ? true : false;

        if($auth->attempt([
            'username' => $request->input('username'),
            'password' => $request->input('password')
        ], $remember)) {
            $events->dispatch('auth.login.successful', [$auth->user()]);

            $path = $session->pull('url.intended', $preferences->get('modules.auth::dashboard'));

            $user = $auth->user();
            assert($user instanceof User);
            return notify(
                new Notification(
                    __('oxygen/mod-auth::messages.login.successful', ['name' => $user->getFullName()])
                ),
                ['redirect' => $path, 'hardRedirect' => true]
            );
        } else {
            $events->dispatch('auth.login.failed', [$request->input('username')]);

            return notify(
                new Notification(
                    __('oxygen/mod-auth::messages.login.failed'),
                    Notification::FAILED
                )
            );
        }
    }

    /**
     * Log the user out.
     * 
     * @param StatefulGuard $auth
     * @param Dispatcher $events
     * @return mixed
     */
    public function postLogout(StatefulGuard $auth, Dispatcher $events) {
        $user = $auth->user();

        $auth->logout();

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
     * @param Guard $auth
     * @return View
     */
    public function getInfo(Guard $auth) {
        $user = $auth->user();

        return view('oxygen/mod-auth::profile', [
            'user' => $user,
            'fields' => $this->crudFields,
            'title' => __('oxygen/mod-auth::ui.profile.title')
        ]);
    }

    /**
     * Shows the update form.
     *
     * @param Guard $auth
     * @return View
     */
    public function getUpdate(Guard $auth) {
        $user = $auth->user();

        return view('oxygen/mod-auth::update', [
            'user' => $user,
            'fields' => $this->crudFields,
            'title' => __('oxygen/mod-auth::ui.update.title')
        ]);
    }

    /**
     * Updates a the user.
     *
     * @param Guard $auth
     * @param Request $request
     * @return Response
     */
    public function putUpdate(Guard $auth, Request $request) {
        $user = $auth->user();

        return parent::putUpdate($user, $request);
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
    public function getChangePassword(Guard $auth) {
        $user = $auth->user();

        return view('oxygen/mod-auth::changePassword', [
            'user' => $user,
            'title' => __('oxygen/mod-auth::ui.changePassword.title')
        ]);
    }

    /**
     * Change the user's password.
     *
     * @param Guard $auth
     * @param Request $request
     * @param Factory $validationFactory
     * @return Response
     * @throws \Oxygen\Data\Exception\InvalidEntityException
     */
    public function postChangePassword(Guard $auth, Request $request, Factory $validationFactory) {
        $user = $auth->user();
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
    public function deleteForce(Guard $auth) {
        $user = $auth->user();
        $this->repository->delete($user);

        return notify(
            new Notification(__('oxygen/mod-auth::messages.account.terminated')),
            ['redirect' => $this->blueprint->getRouteName('getLogin'), 'hardRedirect' => true]
        );
    }

}