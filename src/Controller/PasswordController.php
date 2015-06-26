<?php

namespace OxygenModule\Auth\Controller;

use App;
use Blueprint;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Input;
use Lang;
use Oxygen\Auth\Repository\UserRepositoryInterface;
use Oxygen\Core\Http\Notification;
use Password;
use Redirect;
use Response;
use URL;
use View;
use Validator;

use Oxygen\Core\Blueprint\BlueprintManager;
use Oxygen\Core\Controller\BlueprintController;

class PasswordController extends BlueprintController {

    /**
     * Constructs the controller.
     *
     * @param UserRepositoryInterface  $users
     * @param BlueprintManager         $manager
     */
    public function __construct(UserRepositoryInterface $users, BlueprintManager $manager) {
        parent::__construct($manager->get('Password'));
        $this->users = $users;
    }

    /**
     * Display the password reminder view.
     *
     * @return Response
     */
    public function getRemind() {
        return View::make('oxygen/mod-auth::password.remind', [
            'title' => Lang::get('oxygen/mod-auth::ui.remind.title')
        ]);
    }

    /**
     * Handle a POST request to remind a user of their password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Response
     */
    public function postRemind(PasswordBroker $password, Request $request) {
        $result = $password->sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(Lang::get('oxygen/mod-auth::messages.reminder.email.subject'));
        });

        switch ($result) {
            case PasswordBroker::RESET_LINK_SENT:
                return Response::notification(
                    new Notification(Lang::get($result), 'success')
                );

            case PasswordBroker::INVALID_USER:
                return Response::notification(
                    new Notification(Lang::get($result), 'failed')
                );
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @return Response
     */
    public function getReset() {
        if(!Input::has('token')) {
            App::abort(404);
        }

        return View::make('oxygen/mod-auth::password.reset', [
            'token' => Input::get('token')
        ]);
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function postReset(Request $request, PasswordBroker $password) {
        $validator = Validator::make(
            $request->all(),
            [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed'
            ]
        );

        if(!$validator->passes()) {
            return Response::notification(
                new Notification($validator->messages()->first(), Notification::FAILED)
            );
        }

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = $password->reset($credentials, function($user, $password) {
            $user->setPassword($password);
            $this->users->persist($user);
        });

        switch ($response) {
            case PasswordBroker::INVALID_PASSWORD:
            case PasswordBroker::INVALID_TOKEN:
            case PasswordBroker::INVALID_USER:
                return Response::notification(
                    new Notification(Lang::get($response), 'failed')
                );
            case PasswordBroker::PASSWORD_RESET:
                return Response::notification(
                    new Notification(Lang::get($response), 'success'),
                    ['redirect' => Blueprint::get('Auth')->getRouteName('getLogin'), 'hardRedirect' => true]
                );
        }
    }

}