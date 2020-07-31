@extends('oxygen/ui-theme::layout.main')

@section('title', __('oxygen/mod-auth::ui.reset.title'))

@section('content')

    <div id="app">
        <div class="login-fullscreen login-theme-{{ Preferences::get('appearance.auth::theme', 'autumn') }}">
            <div class="box container">
                <div class="login-welcome">
                    <img src="{{ Preferences::get('appearance.auth::logo') }}" class="login-logo" />

                    <h1 class="subtitle has-text-centered" style="font-variant: small-caps;">
                        @lang('oxygen/mod-auth::ui.login.welcomeSubtitle')
                    </h1>
                </div>
                <div class="login-welcome">
                    <h1 class="subtitle has-text-centered">
                        @lang('oxygen/mod-auth::ui.reset.title')
                    </h1>
                </div>

                <form action="{{ URL::route($blueprint->getAction('postReset')->getName()) }}" method="post">

                    @csrf
                    
                    <input type="hidden" name="token" value="{{ app('request')->get('token') }}">

                    <b-field label="Email Address" label-position="inside">
                        <b-input name="email" type="email"></b-input>
                    </b-field>
                    
                    <b-field label="New Password" label-position="inside">
                        <b-input name="password" type="password"></b-input>
                    </b-field>

                    <b-field label="New Password Again" label-position="inside">
                        <b-input name="password_confirmation" type="password"></b-input>
                    </b-field>

                    <br>

                    <div class="login-justify-content">
                        <b-button type="is-primary" tag="input" native-type="submit" value="@lang('oxygen/mod-auth::ui.reset.submit')"></b-button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="/oxygen/spa-login.js"></script>

@stop