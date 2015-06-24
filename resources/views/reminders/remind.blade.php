@extends(app('oxygen.layout'))

<?php
use Oxygen\Core\Html\Form\Form;$bodyClasses = [ 'Login-theme--' . Config::get('oxygen/mod-auth::theme') ];
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

        $email = new FieldMetadata();

        <div class="Row--visual">
            {{ Form::text('email', null, [
                'placeholder'   => 'Email',
                'class'         => 'Form-input--fullWidth Form-input--transparent'
            ]) }}
        </div>

        <div class="Row Form-footer">
            <button type="submit" class="Button Button-color--blue Button--stretch">
                {{{ Lang::get('oxygen/mod-auth::ui.remind.submit') }}}
            </button>
        </div>

        <div class="Row--visual">
            <a href="{{{ URL::route(Blueprint::get('Auth')->getRouteName('getLogin')) }}}">
                @lang('oxygen/mod-auth::ui.remind.backToLogin')
            </a>
        </div>

    {{ Form::close() }}

</div>

@stop
