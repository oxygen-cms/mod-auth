<?php

use Oxygen\Crud\BlueprintTrait\SoftDeleteCrudTrait;
use OxygenModule\Auth\Controller\UsersController;

Blueprint::make('User', function($blueprint) {
    $blueprint->setController(UsersController::class);
    $blueprint->setIcon('user');

    $blueprint->setToolbarOrders([
        'section' => [
            'getCreate', 'getTrash'
        ],
        'item' => [
            'getUpdate,More' => ['getInfo', 'deleteDelete', 'postRestore', 'deleteForce']
        ]
    ]);

    $blueprint->useTrait(new SoftDeleteCrudTrait());
});
