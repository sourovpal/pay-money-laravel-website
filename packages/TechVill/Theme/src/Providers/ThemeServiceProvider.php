<?php

namespace TechVill\Theme\Providers;

use App;
use File;
use Illuminate\Support\ServiceProvider;
use TechVill\Theme\Console\ThemeListCommand;
use TechVill\Theme\Contracts\ThemeContract;
use TechVill\Theme\Managers\Theme;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!File::exists(public_path('Themes')) && !File::exists(config('theme.symlink_path')) && config('theme.symlink') && File::exists(config('theme.theme_path'))) {
            App::make('files')->link(config('theme.theme_path'), config('theme.symlink_path', public_path('Themes')));
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishConfig();
        $this->registerTheme();
        $this->registerHelper();
        $this->registerMiddleware();
        $this->loadViewsFrom(__DIR__.'/../Views', 'themevel');
    }

    /**
     * Add Theme Types Middleware.
     *
     * @return void
     */
    public function registerMiddleware()
    {
        if (config('theme.types.enable')) {
            $themeTypes = config('theme.types.middleware');
            foreach ($themeTypes as $middleware => $themeName) {
                $this->app['router']->aliasMiddleware($middleware, '\TechVill\Theme\Middleware\RouteMiddleware:'.$themeName);
            }
        }
    }

    /**
     * Register theme required components .
     *
     * @return void
     */
    public function registerTheme()
    {
        $this->app->singleton(ThemeContract::class, function ($app) {
            $theme = new Theme($app, $this->app['view']->getFinder(), $this->app['config'], $this->app['translator']);

            return $theme;
        });
    }

    /**
     * Register All Helpers.
     *
     * @return void
     */
    public function registerHelper()
    {
        foreach (glob(__DIR__.'/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Publish config file.
     *
     * @return void
     */
    public function publishConfig()
    {
        $configPath = realpath(__DIR__.'/../../config/theme.php');

        $this->publishes([
            $configPath => config_path('theme.php'),
        ]);

        $this->mergeConfigFrom($configPath, 'theme');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
