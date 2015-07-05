<?php


namespace OxygenModule\Auth\Fields;

use Oxygen\Auth\Repository\GroupRepositoryInterface;
use Oxygen\Core\Form\FieldSet;

class FullUserFieldSet extends FieldSet {

    /**
     * Creates the fields in the set.
     *
     * @return array
     */
    public function createFields() {
        return $this->makeFields([
            [
                'name'      => 'id',
                'label'     => 'ID'
            ],
            [
                'name'      => 'username',
                'label'     => 'Username',
                'editable'  => true
            ],
            [
                'name'      => 'fullName',
                'label'     => 'Full Name',
                'type'      => 'text',
                'editable'  => true
            ],
            [
                'name'      => 'email',
                'label'     => 'Email Address',
                'type'      => 'email',
                'editable'  => true
            ],
            [
                'name'      => 'preferences',
                'label'     => 'Preferences',
                'type'      => 'editor-mini',
                'editable'  => true,
                'options' => [
                    'language' => 'json',
                    'mode' => 'code'
                ],
                'attributes' => [ 'rows' => 20 ]
            ],
            [
                'name'      => 'group',
                'label'     => 'Group',
                'editable'  => true,
                'type'      => 'relationship',
                'options'   => [
                    'type'       => 'manyToOne',
                    'blueprint'  => 'Group',
                    'allowNull' => false,
                    'repository' => function() {
                        return app(GroupRepositoryInterface::class);
                    },
                    'nameField' => 'name'
                ],

            ],
            [
                'name'      => 'createdAt',
                'type'      => 'date'
            ],
            [
                'name'      => 'updatedAt',
                'type'      => 'date'
            ],
            [
                'name'      => 'deletedAt',
                'type'      => 'date'
            ]
        ]);
    }

    /**
     * Returns the name of the title field.
     *
     * @return mixed
     */
    public function getTitleFieldName() {
        return 'fullName';
    }
}