<?php

namespace OxygenModule\Auth\Controller;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Lab404\Impersonate\Services\ImpersonateManager;
use Oxygen\Core\Form\FieldMetadata;
use Oxygen\Core\Html\Form\EditableField;
use Oxygen\Core\Html\Form\Label;
use Oxygen\Core\Html\Form\Row;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Exception\InvalidEntityException;
use OxygenModule\Auth\Fields\FullUserFieldSet;
use Response;
use Input;
use Lang;
use View;
use Oxygen\Auth\Repository\UserRepositoryInterface;
use Oxygen\Core\Blueprint\BlueprintManager;
use Oxygen\Crud\Controller\SoftDeleteCrudController;

class UsersController extends SoftDeleteCrudController {

    /**
     * Constructs the PagesController.
     *
     * @param UserRepositoryInterface                    $repository
     * @param BlueprintManager                           $manager
     * @param \OxygenModule\Auth\Fields\FullUserFieldSet $fields
     */
    public function __construct(UserRepositoryInterface $repository, BlueprintManager $manager, FullUserFieldSet $fields) {
        parent::__construct($repository, $manager, $fields);
    }

    /**
     * Checks to see if the passed parameter was an instance
     * of Model, if not it will run a query for the model.
     *
     * @param mixed $item
     * @return object
     */
    protected function getItem($item) {
        if(is_object($item)) {
            $item->setAllFillable(true);
            return $item;
        } else {
            $item = $this->repository->find($item);
            $item->setAllFillable(true);
            return $item;
        }
    }

    /**
     * Shows the create form.
     *
     * @return \Illuminate\View\View
     */
    public function getCreate() {
        $extraFields = [];

        $password = new FieldMetadata('password', 'password', true);
        $field = new EditableField($password);

        $extraFields[] = new Row([new Label($password), $field]);

        return view('oxygen/crud::basic.create', [
            'item' => $this->repository->make(),
            'title' => __('oxygen/crud::ui.resource.create'),
            'fields' => $this->crudFields,
            'extraFields' => $extraFields
        ]);
    }

    /**
     * Creates a new Resource.
     *
     * @param Request $input
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function postCreate(Request $input) {
        try {
            $item = $this->getItem($this->repository->make());
            $item->fromArray($this->transformInput($input->except(['_method', '_token', 'password'])));
            $item->setPassword($input->get('password'));
            $this->repository->persist($item);

            return notify(
                new Notification(__('oxygen/crud::messages.basic.created')),
                ['redirect' => $this->blueprint->getRouteName('getList')]
            );
        } catch(InvalidEntityException $e) {
            return notify(
                new Notification($e->getErrors()->first(), Notification::FAILED),
                ['input' => true]
            );
        }
    }

    /**
     * Logs in as the specified user.
     *
     * @param $id
     * @param Guard $auth
     * @param ImpersonateManager $manager
     * @return \Illuminate\Http\Response
     */
    public function postImpersonate($id, Guard $auth, ImpersonateManager $manager) {
        $otherUser = $this->getItem($id);
        if($auth->user() === $otherUser) {
            return notify(
                new Notification(__('oxygen/mod-auth::messages.cannotImpersonateSameUser'), Notification::FAILED),
            );
        }
        $manager->take($auth->user(), $otherUser);
        return notify(
            new Notification(__('oxygen/mod-auth::messages.impersonated', ['name' => $otherUser->getFullName()])),
            ['refresh' => true]
        );
    }

    /**
     * @param Guard $auth
     * @param ImpersonateManager $manager
     * @return \Illuminate\Http\Response
     */
    public function postLeaveImpersonate(Guard $auth, ImpersonateManager $manager) {
        if($manager->isImpersonating()) {
            $manager->leave();

            return notify(
                new Notification(__('oxygen/mod-auth::messages.impersonationStopped', ['name' => $auth->user()->getFullName()])),
                ['refresh' => true]
            );
        } else {
            return notify(
                new Notification(__('oxygen/mod-auth::messages.notImpersonating'), Notification::FAILED),
            );
        }


    }

}
