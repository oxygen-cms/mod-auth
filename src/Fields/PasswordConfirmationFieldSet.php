<?php


namespace OxygenModule\Auth\Fields;

use Oxygen\Core\Form\FieldSet;

class PasswordConfirmationFieldSet extends FieldSet {

    /**
     * Creates the fields in the set.
     *
     * @return array
     */
    public function createFields() {
        return $this->makeFields([
            [
                'name' => 'email',
                'type' => 'text',
                'editable' => true,
                'attributes' => ['autocomplete' => 'off', 'class' => 'Form-input--fullWidth']
            ],
            [
                'name' => 'password',
                'type' => 'password',
                'editable' => true,
                'attributes' => ['autocomplete' => 'off', 'class' => 'Form-input--fullWidth']
            ],
            [
                'name' => 'password_confirmation',
                'type' => 'password',
                'editable' => true,
                'attributes' => ['autocomplete' => 'off', 'class' => 'Form-input--fullWidth']
            ],
        ]);
    }

    /**
     * Returns the name of the title field.
     *
     * @return mixed
     */
    public function getTitleFieldName() {
        return null;
    }
}