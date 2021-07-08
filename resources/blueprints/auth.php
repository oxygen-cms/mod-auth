<?php

use OxygenModule\Auth\Controller\AuthController;
use Oxygen\Core\Action\Factory\ActionFactory;

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
