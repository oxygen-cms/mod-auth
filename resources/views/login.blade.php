@extends('oxygen/ui-theme::layout.main')

@section('title', __('oxygen/mod-auth::ui.login.title'))

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
                
                <br>

                <form action="{{ URL::route($blueprint->getAction('postLogin')->getName()) }}" method="post">

                    @csrf

                    <b-field label="Username" label-position="inside">
                        <b-input name="username"></b-input>
                    </b-field>

                    <b-field label="Password" label-position="inside">
                        <b-input type="password" name="password"></b-input>
                    </b-field>
                    
                    <br>

                    <div class="login-justify-content">
                        <b-button type="is-primary" tag="input" native-type="submit" value="Login"></b-button>
                        <a href="{{ URL::route(Blueprint::get('Password')->getRouteName('getRemind')) }}">
                            @lang('oxygen/mod-auth::ui.login.forgotPassword')
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="/vendor/oxygen/ui-theme/js/spaLogin.js"></script>
@stop
