<?php

use OxygenModule\Auth\Controller\RemindersController;
use Oxygen\Core\Action\Factory\ActionFactory;

Blueprint::make('Reminders', function($blueprint) {
    $blueprint->setController(RemindersController::class);
    $blueprint->setDisplayName('Reminders', Blueprint::SINGULAR);
    $blueprint->setDisplayName('Reminders', Blueprint::PLURAL);

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