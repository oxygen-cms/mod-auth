<?php

Route::get('2fa-required', '\OxygenModule\Auth\Controller\AuthController@getTwoFactorAuthNotice')->name('2fa.notice');
