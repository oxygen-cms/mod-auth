<?php

Route::get('/oxygen/auth/2fa-required', '\OxygenModule\Auth\Controller\AuthController@getTwoFactorAuthNotice')->name('2fa.notice');

Route::post('/oxygen/api/auth/login-log-entries', '\OxygenModule\Auth\Controller\AuthController@getAuthenticationLogEntries')
    ->name('auth.getAuthenticationLogEntries')
    ->middleware([
        'web', 'oxygen.auth', '2fa.require', 'oxygen.permissions:auth.getAuthenticationLogEntries'
    ]);

Route::post('/oxygen/api/auth/ip-location/{ip}', '\OxygenModule\Auth\Controller\AuthController@getIPGeolocation')
    ->name('auth.getIPGeolocation')
    ->middleware([
        'web', 'oxygen.auth', '2fa.require', 'oxygen.permissions:auth.getAuthenticationLogEntries'
    ]);

