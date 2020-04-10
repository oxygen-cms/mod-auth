@extends(app('oxygen.layout'))

<?php
    use Oxygen\Core\Form\FieldMetadata;
    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Form\Form;
    use Oxygen\Core\Html\Form\Row;
    use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

    $bodyClasses = [ 'Body--noScroll', 'Login--isHidden', 'Login-bodyTransition', 'Login-theme--' . Preferences::get('appearance.auth::theme', 'autumn') ];
    $usePage = false;
?>

@section('content')

<div class="Login-background Login-background--sharp"></div>
<div class="Login-background Login-background--blur"></div>

<div class="Login-welcome Block--mini">
    <h1 class="heading-alpha Text--alignCenter">
        @lang('oxygen/mod-auth::ui.login.welcome')
    </h1>
    <h1 class="heading-beta Text--alignCenter">
        @lang('oxygen/mod-auth::ui.login.welcomeSubtitle')
    </h1>
</div>

<div class="Login-message Block--verticallyCentered">
    <button type="button" class="Button Button--border Login-scrollDown">
        @lang('oxygen/mod-auth::ui.login.scrollToForm')
    </button>
</div>

<div class="Login-form Block Block--mini Block--centered">

<!--    <div class="Header Header--normal Header--condensedWidthCenter">-->
<!--        <h2 class="heading-beta">-->
<!--            @lang('oxygen/mod-auth::ui.login.title')-->
<!--        </h2>-->
<!--    </div>-->

    <?php
        $form = new Form($blueprint->getAction('postLogin'));
        $form->setAsynchronous(true);
        $form->addClass('Form--compact');

        $usernameMetadata = new FieldMetadata('username', 'text', true);
        // $usernameMetadata->placeholder = 'Username';
        $usernameMetadata->attributes = ['autocomplete' => 'off', 'class' => 'Form-input--fullWidth'];
        $label = new \Oxygen\Core\Html\Form\Label($usernameMetadata);
        $usernameRow = new Row([$label, new EditableField($usernameMetadata)]);
        $usernameRow->useDefaults = false;
        $usernameRow->addClass('Row--visual');
        $form->addContent($usernameRow);

        $passwordMetadata = new FieldMetadata('password', 'password', true);
        // $passwordMetadata->placeholder = 'Password';
        $passwordMetadata->attributes = ['autocomplete' => 'off', 'class' => 'Form-input--fullWidth'];
        $label = new \Oxygen\Core\Html\Form\Label($passwordMetadata);
        $passwordRow = new Row([$label, new EditableField($passwordMetadata)]);
        $passwordRow->useDefaults = false;
        $passwordRow->addClass('Row--visual');
        $form->addContent($passwordRow);

        $rememberMe = new FieldMetadata('remember', 'checkbox', true);
        $rememberMe->label = 'Remember Me';
        $rememberMe->options['on'] = '1';
        $rememberMeEditable = new EditableField($rememberMe);
        $rememberMeRow = new Row([$rememberMeEditable, '<br><br>']);
        $rememberMeRow->useDefaults = false;
        $rememberMeRow->addClass('Row--visual');
        $rememberMeRow->addClass('Row--flexSpaceBetween');
        $rememberMeRow->addItem(
                '<a href="' . e(URL::route(Blueprint::get('Password')->getRouteName('getRemind'))) . '">' .
                    __('oxygen/mod-auth::ui.login.forgotPassword') .
                '</a>'
        );
        $form->addContent($rememberMeRow);

        $submit = new SubmitToolbarItem(__('oxygen/mod-auth::ui.login.submit'), 'blue');
        $submit->stretch = true;
        $submitRow = new Row([$submit]);
        $submitRow->useDefaults = false;
        $submitRow->addClass('Row--visual');
        $submitRow->isFooter = true;
        $form->addContent($submitRow);

        echo $form->render();

    ?>

</div>

@stop
