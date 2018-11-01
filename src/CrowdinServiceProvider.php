<?php

namespace MacPaw\LaravelCrowdinIntegration;

use Illuminate\Support\ServiceProvider;

class CrowdinServiceProvider extends ServiceProvider
{
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
     *
     * @codeCoverageIgnore
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/crowdin.php' => config_path('crowdin.php'),
        ]);
        if ($this->app->runningInConsole()) {
            $this->commands([
                \MacPaw\LaravelCrowdinIntegration\Crowdin\AddFile::class,
                \MacPaw\LaravelCrowdinIntegration\Crowdin\DownloadAll::class,
                \MacPaw\LaravelCrowdinIntegration\Crowdin\UpdateFile::class,
                \MacPaw\LaravelCrowdinIntegration\Crowdin\Upload::class,
                \MacPaw\LaravelCrowdinIntegration\Crowdin\Build::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/crowdin.php', 'crowdin');
    }
}