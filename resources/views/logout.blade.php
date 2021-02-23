@extends('admin.layout')

@section('title', __('oxygen/mod-auth::ui.logout.title'))

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

    <script src="/oxygen/dist/js/spaLogin.js"></script>

@stop
