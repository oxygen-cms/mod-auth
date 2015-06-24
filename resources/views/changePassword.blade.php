@extends(app('oxygen.layout'))

@section('content')

<?php

    use Oxygen\Core\Form\FieldMetadata as FieldMeta;
use Oxygen\Core\Html\Form\Label;use Oxygen\Core\Html\Form\Row;use Oxygen\Core\Html\Header\Header;
    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Form\Footer;use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

$header = Header::fromBlueprint(
        $blueprint,
        Lang::get('oxygen/mod-auth::ui.changePassword.title')
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
        echo Form::open([
            'route' => $blueprint->getRouteName('postChangePassword'),
            'class' => 'Form--sendAjax Form--warnBeforeExit Form--submitOnKeydown'
        ]);

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
            echo $row->render();
        }

        $footer = new Row([
            new ButtonToolbarItem(Lang::get('oxygen/mod-auth::ui.changePassword.close'), $blueprint->getAction('getInfo')),
            new SubmitToolbarItem(Lang::get('oxygen/mod-auth::ui.changePassword.save'))
        ]);
        $footer->isFooter = true;

        echo $footer->render();

        echo Form::close();
    ?>
</div>

@stop
