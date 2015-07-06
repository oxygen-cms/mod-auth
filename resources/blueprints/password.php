<?php

use OxygenModule\Auth\Controller\PasswordController;
use Oxygen\Core\Action\Factory\ActionFactory;

Blueprint::make('Password', function($blueprint) {
    $blueprint->setController(PasswordController::class);
    $blueprint->disablePluralForm();

    $factory = new ActionFactory();

    $blueprint->makeAction([
        'name' => 'getRemind',
        'pattern' => 'remind',
        'middleware' => ['oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'postRemind',
        'pattern' => 'remind',
        'method' => 'POST',
        'middleware' => ['oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'getReset',
        'pattern' => 'reset',
        'middleware' => ['oxygen.guest']
    ], $factory);

    $blueprint->makeAction([
        'name' => 'postReset',
        'pattern' => 'logout',
        'method' => 'POST',
        'middleware' => ['oxygen.guest']
    ], $factory);
});