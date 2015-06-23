@extends(app('oxygen.layout'))

<?php
    $bodyClasses = [ 'Body--noScroll', 'Login--isHidden', 'Login-bodyTransition', 'Login-theme--' . Config::get('oxygen.auth::theme') ];
    $usePage = false;
?>

@section('content')

<div class="Login-background Login-background--sharp"></div>
<div class="Login-background Login-background--blur"></div>

<div class="Login-message Block--verticallyCentered">
    <h1 class="heading-alpha text-align-center">
        @lang('oxygen/mod-auth::ui.login.welcome')
    </h1>
    <button type="button" class="Button Button--border Login-scrollDown">
        @lang('oxygen/mod-auth::ui.login.scrollToForm')
    </button>
</div>

<div class="Login-form Block Block--mini Block--transparent Block--centered">

    <div class="Header Header--normal Header--noBorder">
        <h2 class="Header-title Header-title--center heading-beta flex-item">
            @lang('oxygen/mod-auth::ui.login.title')
        </h2>
    </div>

    {{ Form::open(array('route' => $blueprint->getRouteName('postLogin'), 'class' => 'Form--sendAjax Form--compact')) }}

        <div class="Row--visual">
            {{ Form::text('username', null, [
                'autocomplete'  => 'off',
                'placeholder'   => 'Username',
                'class'         => 'Form-input--fullWidth Form-input--transparent'
            ]) }}
        </div>

        <div class="Row--visual">
            {{ Form::input('password', 'password', null, [
                'autocomplete'  => 'off',
                'placeholder'   => 'Password',
                'class'         => 'Form-input--fullWidth Form-input--transparent'
            ]) }}
        </div>

        <div class="Row--visual">
            {{ Form::checkbox('remember', '1', '1', ['id' => 'remember']) }}
            {{ Form::label('remember', 'Remember Me', ['class' => 'Form-checkbox-label']) }}
            <br><br>
            <a href="{{{ URL::route(Blueprint::get('Reminders')->getRouteName('getRemind')) }}}">
                @lang('oxygen/mod-auth::ui.login.forgotPassword')
            </a>
        </div>

        <div class="Row Form-footer">
            <button type="submit" class="Button Button-color--blue Button--stretch">
                {{{ Lang::get('oxygen/mod-auth::ui.login.submit') }}}
            </button>
        </div>

    {{ Form::close() }}

</div>

@stop
