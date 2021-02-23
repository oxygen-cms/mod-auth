<?php

use Carbon\Carbon;
use OxygenModule\Auth\Controller\AuthController;
use Oxygen\Core\Action\Factory\ActionFactory;
use Oxygen\Core\Action\Group;
use Oxygen\Core\Contracts\CoreConfiguration;

use Oxygen\Core\Html\Dialog\Dialog;

Blueprint::make('Auth', function(Oxygen\Core\Blueprint\Blueprint $blueprint) {
    $blueprint->setController(AuthController::class);
    $blueprint->setPluralName('Auth');
    $blueprint->setDisplayName('Profile');
    $blueprint->setPluralDisplayName('Auth');
    $blueprint->setIcon('lock');

    $blueprint->setToolbarOrders([
        'section' => ['getUpdate', 'getChangePassword', 'getPreferences']
    ]);

    $factory = new ActionFactory();

    $blueprint->makeAction([
        'name' => 'getPrepareTwoFactor',
        'pattern' => 'prepareTwoFactor',
        'middleware' => ['web', 'oxygen.auth', '2fa.disabled']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'postConfirmTwoFactor',
        'pattern' => 'confirmTwoFactor',
        'method' => 'POST',
        'middleware' => ['web', 'oxygen.auth', '2fa.disabled']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'getLogin',
        'pattern' => 'login',
        'middleware' => ['web', 'oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'getLogoutSuccess',
        'pattern' => 'logout',
        'middleware' => ['web', 'oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'getInfo',
        'pattern' => 'profile.old'
    ]);
    $blueprint->makeToolbarItem([
        'action' => 'getInfo',
        'label' => 'Profile',
        'icon'  => 'search'
    ]);

    $blueprint->makeAction([
        'name' => 'getUpdate',
        'pattern' => 'update'
    ]);
    $blueprint->makeToolbarItem([
        'action' => 'getUpdate',
        'label' => 'Edit Profile',
        'icon'  => 'pencil'
    ]);

    $blueprint->makeAction([
        'name' => 'putUpdate',
        'pattern' => 'profile',
        'method' => 'PUT'
    ]);

    $blueprint->makeAction([
        'name' => 'getPreferences',
        'pattern' => 'preferences'
    ]);
    $blueprint->makeToolbarItem([
        'action' => 'getPreferences',
        'label' => 'Preferences',
        'icon'  => 'cog'
    ]);

    $blueprint->makeAction([
        'name' => 'deleteForce',
        'pattern' => 'terminateAccount',
        'method' => 'DELETE'
    ]);
    $blueprint->makeToolbarItem([
        'action' => 'deleteForce',
        'label' => 'Terminate Account',
        'color' => 'red',
        'icon' => 'trash-o',
        'dialog' => new Dialog(__('oxygen/mod-auth::dialogs.terminateAccount'))
    ]);
});
