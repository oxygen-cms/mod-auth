@extends(app('oxygen.layout'))

@section('content')

<?php

    use Oxygen\Core\Html\Form\Form;
    use Oxygen\Core\Html\Form\Label;
    use Oxygen\Core\Html\Form\Row;
    use Oxygen\Core\Html\Header\Header;
    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;
    use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

    $header = Header::fromBlueprint(
        $blueprint,
        Lang::get('oxygen/mod-auth::ui.update.title')
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
        $form = new Form($blueprint->getAction('putUpdate'));
        $form->setAsynchronous(true)->setWarnBeforeExit(true)->setSubmitOnShortcutKey(true);

        foreach($fields->getFields() as $field) {
            if(!$field->editable) {
                continue;
            }
            $field = EditableField::fromEntity($field, app('request'), $user);
            $label = new Label($field->getMeta());
            $row = new Row([$label, $field]);
            $form->addContent($row);
        }

        $footer = new Row([
            new ButtonToolbarItem(Lang::get('oxygen/mod-auth::ui.update.close'), $blueprint->getAction('getInfo')),
            new SubmitToolbarItem(Lang::get('oxygen/mod-auth::ui.update.save'))
        ]);
        $footer->isFooter = true;

        $form->addContent($row);

        echo $form->render();
    ?>
</div>

@stop
