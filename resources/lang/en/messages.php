<?php

/*
|--------------------------------------------------------------------------
| Message Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are returned from API calls and inform the user
| if the action was successful or not.
|
*/

return [

    /*
    | ---------------
    | Auth
    | ---------------
    |
    | Authentication related messages.
    */

    'login' => [
        'successful'           => 'Welcome, :name',
        'failed'               => 'Incorrect Username or Password'
    ],

    'impersonated' => 'Now impersonating :name',
    'impersonationStopped' => 'Welcome back - :name!',
    'cannotImpersonateSameUser' => 'Cannot impersonate oneself',
    'notImpersonating' => 'Never started impersonating in the first place',

    'fullNameChanged' => 'Full name updated',

    'twoFactor' => [
        'success' => 'Code accepted',
        'failure' => 'Sorry, but that\'s not a valid code. Try again'
    ],

    'logout' => [
        'successful'          => 'Logout Successful',
    ],

    /*
    | ---------------
    | Preferences
    | ---------------
    |
    | Messages related to the user's preferences.
    */

    'preferences' => [
        'updated'       => 'Preferences Updated',
        'updateFailed'  => 'Preferences Update Failed',
    ],

    /*
    | ---------------
    | Password
    | ---------------
    |
    | Messages related to changing the user's password.
    */

    'password' => [
        'invalid'       => 'The old password field is invalid',
        'changed'       => 'Password Changed',
        'changeFailed'  => 'Password Change Failed'
    ],

    /*
    | ---------------
    | Account
    | ---------------
    |
    | Messages relating to the destruction of the user's account.
    */

    'account' => [
        'terminated'      => 'Your account has been terminated',
        'terminateFailed' => 'Account Termination Failed'
    ],

    /*
    | ---------------
    | Password Reminders
    | ---------------
    |
    | Messages relating to password reminders.
    */

    'reminder' => [
        'email'      => [
            'subject'    => 'Password Reminder'
        ]
    ]

];
