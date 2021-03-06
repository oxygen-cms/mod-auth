@extends('admin.layout')

@section('title', __('oxygen/mod-auth::ui.reset.title'))

@section('content')

    <div id="app">
        <div class="login-fullscreen login-theme-{{ Preferences::get('appearance.auth::theme', 'autumn') }}">
            <div class="box container">
                @include('oxygen/mod-auth::loginLogo')
                <div class="login-title">
                    <h1 class="subtitle has-text-centered">
                        @lang('oxygen/mod-auth::ui.reset.title')
                    </h1>
                </div>

                <form action="{{ URL::route($blueprint->getAction('postReset')->getName()) }}" method="post">

                    @csrf

                    <input type="hidden" name="token" value="{{ app('request')->get('token') }}">

                    <input type="hidden" name="email" value="{{ app('request')->get('email') }}">

                    <b-field label="New Password" label-position="inside">
                        <b-input name="password" type="password"></b-input>
                    </b-field>

                    <b-field label="New Password Again" label-position="inside">
                        <b-input name="password_confirmation" type="password"></b-input>
                    </b-field>

                    <br>

                    <div class="login-justify-content">
                        <b-button type="is-primary" tag="input" native-type="submit" value="@lang('oxygen/mod-auth::ui.reset.submit')"></b-button>
                        <a href=" {{ URL::route(Blueprint::get('Auth')->getRouteName('getLogin')) }} ">
                            @lang('oxygen/mod-auth::ui.remind.backToLogin')
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>

@stop
