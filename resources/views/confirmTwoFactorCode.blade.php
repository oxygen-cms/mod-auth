@extends('admin.layout')

@section('title', __('oxygen/mod-auth::ui.login.title'))

@section('content')

<div id="app">
    <div class="login-fullscreen login-theme-{{ Preferences::get('appearance.auth::theme', 'autumn') }}">
        <div class="box container">
            @include('oxygen/mod-auth::loginLogo')
            <div class="login-title">
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
                    <b-input name="{{ $input }}" id="{{ $input }}" type="number" placeholder="e.g.: 123456" minlength="6" required autofocus></b-input>
                </b-field>

                <br>

                <div class="login-justify-content">
                    <b-button type="is-primary" tag="input" native-type="submit" value="@lang('laraguard::messages.confirm')"></b-button>
                </div>

            </form>

        </div>
    </div>
</div>

@stop
