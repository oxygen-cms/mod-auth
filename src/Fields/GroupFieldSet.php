<?php


namespace OxygenModule\Auth\Fields;

use Oxygen\Core\Form\FieldSet;

class GroupFieldSet extends FieldSet {

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
                'name'      => 'name',
                'editable'  => true
            ],
            [
                'name'      => 'description',
                'editable'  => true,
                'type'      => 'textarea',
                'attributes' => [
                    'rows' => '5'
                ]
            ],
            [
                'name'      => 'preferences',
                'type'      => 'editor-mini',
                'editable'  => true,
                'options' => [
                    'language'  => 'json',
                    'mode'      => 'code'
                ],
                'attributes' => [
                    'rows' => 20
                ]
            ],
            [
                'name'      => 'permissions',
                'type'      => 'editor-mini',
                'editable'  => true,
                'options' => [
                    'language'  => 'json',
                    'mode'      => 'code'
                ],
                'attributes' => [
                    'rows' => 20
                ]
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
        return 'name';
    }
}
