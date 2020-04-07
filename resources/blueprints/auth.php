<?php

use Carbon\Carbon;
use OxygenModule\Auth\Controller\AuthController;
use Oxygen\Core\Action\Factory\ActionFactory;
use Oxygen\Core\Action\Group;
use Oxygen\Core\Contracts\CoreConfiguration;

use Oxygen\Core\Html\Dialog\Dialog;

Blueprint::make('Auth', function($blueprint) {
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
        'name' => 'getCheck',
        'pattern' => '/',
        'group' => new Group('auth', app(CoreConfiguration::class)->getAdminURIPrefix())
    ], $factory);

    $blueprint->makeAction([
        'name' => 'getLogin',
        'pattern' => 'login',
        'middleware' => ['oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'postLogin',
        'pattern' => 'login',
        'method'  => 'POST',
        'middleware' => ['oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'postLogout',
        'pattern' => 'logout',
        'method' => 'POST'
    ]);
    $blueprint->makeToolbarItem([
        'action' => 'postLogout',
        'label' => 'Logout',
        'icon'  => 'power-off'
    ]);

    $blueprint->makeAction([
        'name' => 'getLogoutSuccess',
        'pattern' => 'logout',
        'middleware' => ['oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'getInfo',
        'pattern' => 'profile'
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
        'name' => 'getChangePassword',
        'pattern' => 'changePassword'
    ]);
    $blueprint->makeToolbarItem([
        'action' => 'getChangePassword',
        'label' => 'Change Password',
        'icon'  => 'lock'
    ]);

    $blueprint->makeAction([
        'name' => 'postChangePassword',
        'pattern' => 'changePassword',
        'method' => 'POST'
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