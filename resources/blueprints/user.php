<?php

use Oxygen\Core\Action\Factory\ActionFactory;
use Oxygen\Core\Html\Dialog\Dialog;
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
            'getUpdate,More' => ['getInfo', 'postImpersonate', 'deleteDelete', 'postRestore', 'deleteForce']
        ]
    ]);

    $blueprint->makeAction([
        'name' => 'postImpersonate',
        'pattern' => '{id}/impersonate',
        'method' => 'POST'
    ]);
    $blueprint->makeToolbarItem([
        'action' => 'postImpersonate',
        'label' => 'Login as this user',
        'icon' => 'lock',
        'dialog' => new Dialog(__('oxygen/mod-auth::dialogs.loginAs'))
    ]);

    $factory = new ActionFactory();
    $blueprint->makeAction([
        'name' => 'postLeaveImpersonate',
        'pattern' => 'leaveImpersonate',
        'middleware' => ['web', 'oxygen.auth', '2fa.require'],
        'method' => 'POST'
    ], $factory);

    $blueprint->useTrait(new SoftDeleteCrudTrait());
    $blueprint->useTrait(new SearchableCrudTrait());
});
