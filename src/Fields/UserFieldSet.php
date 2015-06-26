<?php


namespace OxygenModule\Auth\Fields;

use Carbon\Carbon;
use Oxygen\Core\Form\FieldSet;
use Oxygen\Core\Form\Type\CustomType;

class UserFieldSet extends FieldSet {

    /**
     * Creates the fields in the set.
     *
     * @return array
     */
    public function createFields() {
        return $this->makeFields([
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
                'name'      => 'group',
                'label'     => 'Group',
                'typeInstance' => new CustomType(
                    function($metadata, $value) {
                        return $value;
                    },
                    function($metadata, $value) {
                        return $value->getName();
                    }
                )
            ],
            [
                'name'      => 'createdAt',
                'label'     => 'Joined',
                'type'      => 'datetime',
                'typeInstance' => new CustomType(
                    function($metadata, $value) {
                        return $value;
                    },
                    function($metadata, $value) {
                        return Carbon::instance($value)->diffForHumans();
                    }
                )
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