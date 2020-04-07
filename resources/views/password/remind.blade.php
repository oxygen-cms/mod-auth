@extends(app('oxygen.layout'))

<?php
use Oxygen\Core\Form\FieldMetadata;use Oxygen\Core\Html\Form\EditableField;use Oxygen\Core\Html\Form\Form;use Oxygen\Core\Html\Form\Row;use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

$bodyClasses = [ 'Login-theme--' . Preferences::get('appearance.auth::theme') ];
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
            @lang('oxygen/mod-auth::ui.remind.title')
        </h2>
    </div>

    <?php
        $form = new Form($blueprint->getAction('postRemind'));
        $form->setAsynchronous(true)->addClass('Form--compact');

        $email = new FieldMetadata('email', 'text', true);
        $email->placeholder = 'Email';
        $email->attributes['class'] = 'Form-input--fullWidth Form-input--transparent';
        $emailRow = new Row([new EditableField($email, app('request'))]);
        $emailRow->useDefaults = false;
        $emailRow->addClass('Row--visual');
        $form->addContent($emailRow);

        $submit = new SubmitToolbarItem(__('oxygen/mod-auth::ui.remind.submit'), 'blue');
        $submit->stretch = true;
        $submitRow = new Row([$submit]);
        $submitRow->isFooter = true;
        $form->addContent($submitRow);

        $back = new Row(['<a href="' . e(URL::route(Blueprint::get('Auth')->getRouteName('getLogin'))) . '">' .
                e(__('oxygen/mod-auth::ui.remind.backToLogin'))  .'</a>']);
        $back->useDefaults = false;
        $back->addClass('Row--visual');

        $form->addContent($back);

        echo $form->render();

    ?>

</div>

@stop
