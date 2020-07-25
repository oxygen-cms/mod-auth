@extends('oxygen/ui-theme::layout.main')

@section('content')

<div id="app">
    <div class="login-fullscreen login-theme-{{ Preferences::get('appearance.auth::theme', 'autumn') }}">
        <div class="box container is-wider">
            <div class="login-welcome">
                <img src="{{ Preferences::get('appearance.auth::logo') }}" class="login-logo" />

                <h1 class="subtitle has-text-centered" style="font-variant: small-caps;">
                    @lang('oxygen/mod-auth::ui.login.welcomeSubtitle')
                </h1>
            </div>
            <div class="login-welcome">
                <h1 class="subtitle has-text-centered">
                    @lang('oxygen/mod-auth::ui.prepareTwoFactorAuth.title')
                </h1>
            </div>

            <p>Two-factor authentication uses once-off codes from another device (e.g.: your phone) for additional security. To use two-factor authentication with Oxygen, you need to download an authenticator app onto your phone. Supported apps include Google Authenticator, LastPass Authenticator, Microsoft Authenticator, Authy etc... You can download Google Authenticator using the buttons below:</p>

            <a target="_blank" href='https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en_AU&pcampaignid=pcampaignidMKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1'><img alt='Get it on Google Play' src='https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png' style="width: 120px;"/></a>
            <a target="_blank" href="https://apps.apple.com/us/app/google-authenticator/id388497605"><img src="https://developer.apple.com/app-store/marketing/guidelines/images/badge-download-on-the-app-store.svg" style="width: 120px;" /></a>

            <br>
            <h2 class="subtitle">Getting started</h2>
            <p>Scan the QR code below on your phone to begin setting up two-factor authentication, or follow <a href="{{ $as_uri }}">this link</a>.</p>

            {!! $as_qr_code !!}
            
            <p></p>
            <p>Or, manually enter this secret key into your chosen authenticator app:<br><br>
            <pre>{{ $as_string }}</pre></p>

            <br>
            <h2 class="subtitle">Confirm setup</h2>
            <p>Once you've successfully configured your authenticator app, enter the code below to continue.</p>
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

<script src="/vendor/oxygen/ui-theme/js/spaLogin.js"></script>

@stop
