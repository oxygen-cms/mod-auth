<?php

    use Oxygen\Preferences\Loader\Database\PreferenceRepositoryInterface;
    use Oxygen\Preferences\Loader\DatabaseLoader;

Preferences::register('modules.auth', function($schema) {
    $schema->setTitle('Authentication');
    $schema->setLoader(new DatabaseLoader(app(PreferenceRepositoryInterface::class), 'modules.auth'));

    $routesByName = function() {
        $options = [];
        foreach(Route::getRoutes()->getRoutes() as $route) {
            $name = $route->getName();
            if($name !== null) {
                $options[$name] = $name;
            }
        }
        return $options;
    };

    $schema->makeFields([
        '' => [
            '' => [
                [
                    'name' => 'dashboard',
                    'type' => 'select',
                    'options' => $routesByName,
                    'validationRules' => ['route_exists:name']
                ],
                [
                    'name' => 'home',
                    'type' => 'select',
                    'options' => $routesByName,
                    'validationRules' => ['route_exists:name']
                ]
            ]
        ]
    ]);
});