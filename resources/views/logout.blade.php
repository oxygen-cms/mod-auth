@extends('oxygen/ui-theme::layout.main')

@section('content')

    <div id="app">
        <div class="login-fullscreen login-theme-{{ Preferences::get('appearance.auth::theme', 'autumn') }}">
            <div class="box container">
                <div class="login-welcome">
                    <h1 class="subtitle has-text-centered">
                        @lang('oxygen/mod-auth::ui.logout.title')
                    </h1>
                </div>

                <div class="login-justify-content">
                    <b-button tag="a" href="{{{ URL::route(Preferences::get('modules.auth::home')) }}}" type="is-text">
                        @lang('oxygen/mod-auth::ui.logout.toHome')
                    </b-button>
                    <b-button tag="a" href="{{{ URL::route($blueprint->getRouteName('getLogin')) }}}" type="is-primary">
                        @lang('oxygen/mod-auth::ui.logout.loginAgain')
                    </b-button>
                </div>

            </div>
        </div>
    </div>

    <script src="/vendor/oxygen/ui-theme/js/spaLogin.js"></script>


<!--<div class="Login-background Login-background--sharp"></div>-->
<!---->
<!-- =====================-->
<!--            Logout-->
<!--     ===================== -->
<!---->
<!--<div class="CenteringContainer">-->
<!--    <div class="Block Block--mini">-->
<!--        <div class="Header Header--normal Header--condensedWidthCenter">-->
<!--            <h2 class="Header-title heading-beta">-->
<!--                -->
<!--            </h2>-->
<!--        </div>-->
<!---->
<!--        <div class="Row Row--flexSpaceBetween">-->
<!--            <a href="{{{ URL::route($blueprint->getRouteName('getLogin')) }}}" class="Button Button-color--blue">-->
<!--                @lang('oxygen/mod-auth::ui.logout.loginAgain')-->
<!--            </a>-->
<!--            <a href="{{{ URL::route(Preferences::get('modules.auth::home')) }}}" class="Button Button-color--grey">-->
<!--                @lang('oxygen/mod-auth::ui.logout.toHome')-->
<!--            </a>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->



    @stop
