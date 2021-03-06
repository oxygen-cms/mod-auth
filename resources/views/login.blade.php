@extends('admin.layout')

@section('title', __('oxygen/mod-auth::ui.login.title'))

@section('content')
    <div id="app">
        <div class="login-fullscreen login-theme-{{ Preferences::get('appearance.auth::theme', 'autumn') }}">
            <div class="box container">
                @include('oxygen/mod-auth::loginLogo')

                <br>

                <form action="/oxygen/api/auth/login" method="post">

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

@stop
