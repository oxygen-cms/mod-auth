@extends(app('oxygen.layout'))

@section('content')

<?php

    use Oxygen\Core\Form\FieldMetadata as FieldMeta;
    use Oxygen\Core\Html\Form\Form;
    use Oxygen\Core\Html\Form\Label;
    use Oxygen\Core\Html\Form\Row;
    use Oxygen\Core\Html\Header\Header;
    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;
    use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

    $header = Header::fromBlueprint(
        $blueprint,
        __('oxygen/mod-auth::ui.changePassword.title')
    );

    $header->setBackLink(URL::route($blueprint->getRouteName('getInfo')));

?>

<!-- =====================
            HEADER
     ===================== -->

<div class="Block">
    {!! $header->render() !!}
</div>

<!-- =====================
            FORM
     ===================== -->

<div class="Block">
    <?php
        $form = new Form($blueprint->getAction('postChangePassword'));
        $form->setAsynchronous(true)->setSubmitOnShortcutKey(true)->setWarnBeforeExit(true);

        $fields = [
            new FieldMeta('oldPassword', 'password', true),
            new FieldMeta('password', 'password', true),
            new FieldMeta('passwordConfirmation', 'password', true)
        ];

        foreach($fields as $field) {
            if(!$field->editable) {
                continue;
            }
            $field = new EditableField($field, app('request'));
            $label = new Label($field->getMeta());
            $row = new Row([$label, $field]);
            $form->addContent($row);
        }

        $footer = new Row([
            new ButtonToolbarItem(__('oxygen/mod-auth::ui.changePassword.close'), $blueprint->getAction('getInfo')),
            new SubmitToolbarItem(__('oxygen/mod-auth::ui.changePassword.save'))
        ]);
        $footer->isFooter = true;

        $form->addContent($footer);

        echo $form->render();
    ?>
</div>

@stop
