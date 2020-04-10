<?php

use Oxygen\Crud\BlueprintTrait\SoftDeleteCrudTrait;
use OxygenModule\Auth\Controller\UsersController;
use Oxygen\Crud\BlueprintTrait\SearchableCrudTrait;

Blueprint::make('User', function(Oxygen\Core\Blueprint\Blueprint $blueprint) {
    $blueprint->setController(UsersController::class);
    $blueprint->setIcon('user');

    $blueprint->setToolbarOrders([
        'section' => [
            'getList.search', 'getCreate', 'getTrash'
        ],
        'item' => [
            'getUpdate,More' => ['getInfo', 'deleteDelete', 'postRestore', 'deleteForce']
        ]
    ]);

    $blueprint->useTrait(new SoftDeleteCrudTrait());
    $blueprint->useTrait(new SearchableCrudTrait());
});
