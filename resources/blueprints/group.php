<?php

use Oxygen\Crud\BlueprintTrait\SoftDeleteCrudTrait;
use OxygenModule\Auth\Controller\GroupsController;

Blueprint::make('Group', function($blueprint) {
    $blueprint->setController(GroupsController::class);
    $blueprint->setIcon('users');

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