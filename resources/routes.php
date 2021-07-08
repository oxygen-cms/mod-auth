<?php

Route::get('/oxygen/auth/2fa-required', '\OxygenModule\Auth\Controller\AuthController@getTwoFactorAuthNotice')->name('2fa.notice');


Route::post('/oxygen/api/auth/login', '\OxygenModule\Auth\Controller\AuthController@postLogin')
    ->name('auth.postLogin')
    ->middleware([
        'web', 'oxygen.guest'
    ]);

Route::post('/oxygen/api/auth/logout', '\OxygenModule\Auth\Controller\AuthController@postLogout')
    ->name('auth.postLogout')
    ->middleware([
        'web', 'oxygen.auth', '2fa.require'
    ]);

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

Route::get('/oxygen/api/auth/user', '\OxygenModule\Auth\Controller\AuthController@getUserDetailsApi')
    ->name('auth.getInfo')
    ->middleware([
        'web', 'oxygen.auth', '2fa.require', 'oxygen.permissions:auth.getInfo'
    ]);

Route::put('/oxygen/api/auth/fullName', '\OxygenModule\Auth\Controller\AuthController@putUpdateFullName')
    ->name('auth.putUpdateFullName')
    ->middleware([
        'web', 'oxygen.auth', '2fa.require', 'oxygen.permissions:auth.putUpdate'
    ]);

Route::post('/oxygen/api/auth/change-password', '\OxygenModule\Auth\Controller\AuthController@postChangePassword')
    ->name('auth.postChangePassword')
    ->middleware([
        'web', 'oxygen.auth', '2fa.require', 'oxygen.permissions:auth.postChangePassword'
    ]);

Route::post('/oxygen/api/auth/terminate-account', '\OxygenModule\Auth\Controller\AuthController@deleteForce')
    ->name('auth.deleteForce')
    ->middleware([
        'web', 'oxygen.auth', '2fa.require', 'oxygen.permissions:auth.deleteForce'
    ]);
