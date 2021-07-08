@extends('admin.layout')

@section('title', __('oxygen/mod-auth::ui.prepareTwoFactorAuth.title'))

@section('content')

<div id="app">
    <div class="login-fullscreen login-theme-{{ Preferences::get('appearance.auth::theme', 'autumn') }}">
        <div class="box container is-wider">
            @include('oxygen/mod-auth::loginLogo')

            <div class="login-title">
                <h1 class="subtitle has-text-centered">
                    @lang('oxygen/mod-auth::ui.prepareTwoFactorAuth.title')
                </h1>
            </div>

            <p>Two-factor authentication uses once-off codes from another device (e.g.: your phone) for additional security. To use two-factor authentication with Oxygen CMS, you need to download an authenticator app onto your phone. Supported apps include
                Google Authenticator,
                <a href="https://lastpass.com/auth/" target="_blank">LastPass Authenticator</a>,
                <a href="https://www.microsoft.com/en-us/account/authenticator" target="_blank">Microsoft Authenticator</a>,
                <a href="https://authy.com/features/" target="_blank">Authy</a>...</p>

            <br>
            <h2 class="subtitle">Getting started</h2>
            <p>Scan the QR code below on your phone to begin setting up two-factor authentication, or follow <a href="{{ $as_uri }}">this link</a>.</p>

            {!! $as_qr_code !!}

            <p></p>
            <p>Or, manually enter this secret key into your chosen authenticator app:<br><br>
            <pre>{{ $as_string }}</pre></p>

            <br>
            <h2 class="subtitle">Confirm setup</h2>
            <p>Once you've successfully conf(igured your authenticator app, enter the code below to continue.</p>
            <br>

            <form action="{{ URL::route($blueprint->getAction('postConfirmTwoFactor')->getName()) }}" method="post">

                @csrf

                <div class="login-input-align-left">
                    <b-field label="2FA Code" label-position="inside">
                        <b-input name="2fa_code" type="number" minlength="6" placeholder="enter code here" required></b-input>
                    </b-field>
                </div>

                <br>

                <div class="login-justify-content">
                    <b-button type="is-primary" tag="input" native-type="submit" value="@lang('oxygen/mod-auth::ui.prepareTwoFactorAuth.submit')">Foobar</b-button>
                </div>

            </form>

        </div>
    </div>
</div>

@stop
