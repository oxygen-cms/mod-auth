@extends(app('oxygen.layout'))

<?php
use Oxygen\Core\Form\FieldMetadata;use Oxygen\Core\Html\Form\EditableField;use Oxygen\Core\Html\Form\Form;use Oxygen\Core\Html\Form\Row;use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;use OxygenModule\Auth\Fields\PasswordConfirmationFieldSet;$bodyClasses = [ 'Login-theme--' . Preferences::get('appearance.auth::theme') ];
    $usePage = false;
?>

@section('content')

<div class="Login-background Login-background--sharp"></div>

<!-- =====================
            Logout
     ===================== -->

<div class="Block Block--mini Block--transparent Block--centered">

    <div class="Header Header--noBorder">
        <h2 class="Header-title heading-beta flex-item">
            @lang('oxygen/mod-auth::ui.reset.title')
        </h2>
    </div>

    <?php

        $form = new Form($blueprint->getAction('postReset'));
        $form->setAsynchronous(true);
        $form->addClass('Form--compact');

        $token = new FieldMetadata('token', 'hidden', true);
        $tokenRow = new Row([new EditableField($token, app('request'))]);
        $tokenRow->useDefaults = false;
        $form->addContent($tokenRow);

        foreach(app(PasswordConfirmationFieldSet::class)->getFields() as $field) {
            $editable = new EditableField($field, app('request'));
            $row = new Row([$editable]);
            $row->useDefaults = false;
            $row->addClass('Row--visual');
            $form->addContent($row);
        }

        $submit = new SubmitToolbarItem(Lang::get('oxygen/mod-auth::ui.reset.submit'), 'blue');
        $submit->stretch = true;
        $row = new Row([$submit]);
        $row->isFooter = true;
        $form->addContent($row);

        echo $form->render();

    ?>

</div>

@stop
