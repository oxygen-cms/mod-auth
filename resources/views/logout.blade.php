@extends(app('oxygen.layout'))

<?php
    $bodyClasses = [ 'Login-theme--' . Preferences::get('appearance.auth::theme') ];
    $usePage = false;
?>

@section('content')

<div class="Login-background Login-background--sharp"></div>

<!-- =====================
            Logout
     ===================== -->

<div class="Block Block--mini Block--centered">

    <div class="Header Header--normal Header--condensedWidthCenter">
        <h2 class="Header-title heading-beta">
            @lang('oxygen/mod-auth::ui.logout.title')
        </h2>
    </div>

    <div class="Row Row--flexSpaceBetween">
        <a href="{{{ URL::route($blueprint->getRouteName('getLogin')) }}}" class="Button Button-color--blue">
            @lang('oxygen/mod-auth::ui.logout.loginAgain')
        </a>
        <a href="{{{ URL::route(Preferences::get('modules.auth::home')) }}}" class="Button Button-color--grey">
            @lang('oxygen/mod-auth::ui.logout.toHome')
        </a>
    </div>
</div>

@stop
