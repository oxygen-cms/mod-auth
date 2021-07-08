<?php

namespace OxygenModule\Auth;

use Illuminate\Support\ServiceProvider;
use Oxygen\Core\Blueprint\BlueprintManager;
use Oxygen\Preferences\PreferencesManager;
use Oxygen\Preferences\Transformer\JavascriptTransformer;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'oxygen.auth');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'oxygen/mod-auth');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'oxygen/mod-auth');

        $this->loadRoutesFrom(__DIR__ . '/../resources/routes.php');

        $this->publishes([
            __DIR__ . '/../resources/lang' => base_path('resources/lang/vendor/oxygen/mod-auth'),
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/oxygen/mod-auth')
        ]);

        $this->app[BlueprintManager::class]->loadDirectory(__DIR__ . '/../resources/blueprints');
        $this->app[PreferencesManager::class]->loadDirectory(__DIR__ . '/../resources/preferences');
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */

	public function register() {}

}
