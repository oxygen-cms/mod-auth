@extends('oxygen/ui-theme::layout.main')

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
                    @lang('laraguard::messages.required')
                </h1>
            </div>

            <form action="{{ $action }}" method="post">

                @csrf

                @foreach((array)$credentials as $name => $value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach

                @if($remember)
                <input type="hidden" name="remember" value="on">
                @endif

                <p>
                    {{ trans('laraguard::messages.continue') }}
                </p>
                
                <br>

                <b-field label="2FA Code" label-position="inside">
                    <b-input name="{{ $input }}" id="{{ $input }}" type="number" placeholder="e.g.: 123456" minlength="6" required></b-input>
                </b-field>
                
<!--                <div class="Row Row--visual">-->
<!--                    <input type="number"-->
<!--                           class="@if($error) is-invalid @endif"-->
<!--                           minlength="6" required>-->
<!--                </div>-->

                @if($error)
                    <b-notification aria-close-label="Close notification" type="is-danger" :closable="false">
                        @lang('laraguard::validation.totp_code')
                    </b-notification>
                @endif

                <br>

                <div class="login-justify-content">
                    <b-button type="is-primary" tag="input" native-type="submit" value="@lang('laraguard::messages.confirm')"></b-button>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="/vendor/oxygen/ui-theme/js/spaLogin.js"></script>

@stop