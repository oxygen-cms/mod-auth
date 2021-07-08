@extends('admin.layout')

@section('title', __('oxygen/mod-auth::ui.remind.title'))

@section('content')

    <div id="app">
        <div class="login-fullscreen login-theme-{{ Preferences::get('appearance.auth::theme', 'autumn') }}">
            <div class="box container">
                @include('oxygen/mod-auth::loginLogo')
                <div class="login-title">
                    <h1 class="subtitle has-text-centered">
                        @lang('oxygen/mod-auth::ui.remind.title')
                    </h1>
                </div>

                <form action="{{ URL::route($blueprint->getAction('postRemind')->getName()) }}" method="post">

                    @csrf

                    <b-field label="Email Address" label-position="inside">
                        <b-input name="email" type="email"></b-input>
                    </b-field>

                    <br>

                    <div class="login-justify-content">
                        <b-button type="is-primary" tag="input" native-type="submit" value="@lang('oxygen/mod-auth::ui.remind.submit')"></b-button>
                        <a href=" {{ URL::route(Blueprint::get('Auth')->getRouteName('getLogin')) }} ">
                            @lang('oxygen/mod-auth::ui.remind.backToLogin')
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>

@stop
