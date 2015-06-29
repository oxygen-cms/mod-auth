<?php

namespace OxygenModule\Auth\Controller;

use Exception;

use Auth;
use Config;
use Hash;
use Event;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\UrlGenerator;
use Input;
use Oxygen\Auth\Repository\UserRepositoryInterface;
use Oxygen\Core\Contracts\Routing\ResponseFactory;
use Oxygen\Preferences\PreferencesManager;
use OxygenModule\Auth\Fields\UserFieldSet;
use Redirect;
use Response;
use URL;
use View;
use Lang;
use Validator;
use Preferences;

use Oxygen\Crud\Controller\BasicCrudController;
use Oxygen\Core\Http\Notification;
use Oxygen\Core\Blueprint\BlueprintManager;

class AuthController extends BasicCrudController {

    /**
     * Constructs the AuthController.
     *
     * @param UserRepositoryInterface                    $repository
     * @param BlueprintManager                           $manager
     * @param \OxygenModule\Auth\Fields\UserFieldSet $fieldSet
     */
    public function __construct(UserRepositoryInterface $repository, BlueprintManager $manager, UserFieldSet $fieldSet) {
        parent::__construct($repository, $manager->get('Auth'), $fieldSet);
    }

    /**
     * If logged in, redirect to the dashboard.
     * If not, show the login page.
     *
     * @return Response
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
     * @return Response
     */
    public function getLogin() {
        return View::make('oxygen/mod-auth::login', [
            'title' => Lang::get('oxygen/mod-auth::ui.login.title')
        ]);
    }

    /**
     * Login action.
     *
     * @return Response
     */
    public function postLogin() {
        $remember = Input::get('remember') === '1' ? true : false;

        if(Auth::attempt([
            'username' => Input::get('username'),
            'password' => Input::get('password')
        ], $remember)) {
            Event::fire('auth.login.successful', [Auth::user()]);

            return Response::notification(
                new Notification(
                    Lang::get('oxygen/mod-auth::messages.login.successful', ['name' => Auth::user()->getFullName()])
                ),
                ['redirect' => Preferences::get('modules.auth::dashboard'), 'hardRedirect' => true]
            );
        } else {
            Event::fire('auth.login.failed', [Input::get('username')]);

            return Response::notification(
                new Notification(
                    Lang::get('oxygen/mod-auth::messages.login.failed'),
                    Notification::FAILED
                )
            );
        }
    }

    /**
     * Log the user out.
     *
     * @return Response
     */
    public function postLogout() {
        $user = Auth::user();

        Auth::logout();

        Event::fire('auth.logout.successful', [$user]);

        return Response::notification(
            new Notification(Lang::get('oxygen/mod-auth::messages.logout.successful')),
            ['redirect' => 'auth.getLogoutSuccess', 'hardRedirect' => true] // redirect without SmoothState
        );
    }

    /**
     * Show the logout success message.
     *
     * @return Response
     */
    public function getLogoutSuccess() {
        return View::make('oxygen/mod-auth::logout', [
            'title' => Lang::get('oxygen/mod-auth::ui.logout.title')
        ]);
    }

    /**
     * Show the current user's profile.
     *
     * @param mixed $foo useless param
     * @return Response
     */
    public function getInfo($foo = null) {
        $user = Auth::user();

        return View::make('oxygen/mod-auth::profile', [
            'user' => $user,
            'fields' => $this->crudFields,
            'title' => Lang::get('oxygen/mod-auth::ui.profile.title')
        ]);
    }

    /**
     * Shows the update form.
     *
     * @param mixed $foo useless param
     * @return Response
     */
    public function getUpdate($foo = null) {
        $user = Auth::user();

        return View::make('oxygen/mod-auth::update', [
            'user' => $user,
            'fields' => $this->crudFields,
            'title' => Lang::get('oxygen/mod-auth::ui.update.title')
        ]);
    }

    /**
     * Updates a the user.
     *
     * @param mixed                                          $foo useless param
     * @param \Oxygen\Core\Contracts\Routing\ResponseFactory $response
     * @return \Illuminate\Http\Response
     */
    public function putUpdate($foo = null, ResponseFactory $response) {
        $user = Auth::user();

        return parent::putUpdate($user, $response);
    }

    /**
     * Redirects the user to the preferences.
     *
     * @return Response
     */
    public function getPreferences(ResponseFactory $response) {
        return $response->redirectToRoute('preferences.getView', ['user']);
    }

    /**
     * Change password form.
     *
     * @return Response
     */
    public function getChangePassword() {
        $user = Auth::user();

        return View::make('oxygen/mod-auth::changePassword', [
            'user' => $user,
            'title' => Lang::get('oxygen/mod-auth::ui.changePassword.title')
        ]);
    }

    /**
     * Change the user's password.
     *
     * @return Response
     */
    public function postChangePassword() {
        $user = Auth::user();
        $input = Input::all();

        $validator = Validator::make(
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

            return Response::notification(
                new Notification(Lang::get('oxygen/mod-auth::messages.password.changed')),
                ['redirect' => $this->blueprint->getRouteName('getInfo')]
            );
        } else {
            return Response::notification(
                new Notification($validator->messages()->first(), Notification::FAILED)
            );
        }
    }

    /**
     * Deletes the user permanently.
     *
     * @return Response
     */
    public function deleteForce() {
        $user = Auth::user();
        $this->repository->delete($user);

        return Response::notification(
            new Notification(Lang::get('oxygen/mod-auth::messages.account.terminated')),
            ['redirect' => $this->blueprint->getRouteName('getLogin'), 'hardRedirect' => true]
        );
    }

}