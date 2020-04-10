<?php

use Oxygen\Crud\BlueprintTrait\SoftDeleteCrudTrait;
use OxygenModule\Auth\Controller\GroupsController;
use Oxygen\Crud\BlueprintTrait\SearchableCrudTrait;

Blueprint::make('Group', function(Oxygen\Core\Blueprint\Blueprint $blueprint) {
    $blueprint->setController(GroupsController::class);
    $blueprint->setIcon('users');

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