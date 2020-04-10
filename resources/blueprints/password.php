<?php

use OxygenModule\Auth\Controller\PasswordController;
use Oxygen\Core\Action\Factory\ActionFactory;

Blueprint::make('Password', function(Oxygen\Core\Blueprint\Blueprint $blueprint) {
    $blueprint->setController(PasswordController::class);
    $blueprint->disablePluralForm();

    $factory = new ActionFactory();

    $blueprint->makeAction([
        'name' => 'getRemind',
        'pattern' => 'remind',
        'middleware' => ['web', 'oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'postRemind',
        'pattern' => 'remind',
        'method' => 'POST',
        'middleware' => ['web', 'oxygen.guest']
    ], $factory);

    // named `reset` to make it compatible with Laravel's default password reset notification,
    // which wants to redirect to the `password.reset` route
    $blueprint->makeAction([
        'name' => 'reset',
        'pattern' => 'reset',
        'middleware' => ['web', 'oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'postReset',
        'pattern' => 'logout',
        'method' => 'POST',
        'middleware' => ['web', 'oxygen.guest']
    ], $factory);
});