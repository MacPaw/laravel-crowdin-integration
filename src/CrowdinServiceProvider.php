<?php

namespace MacPaw\LaravelCrowdinIntegration;

use Illuminate\Support\ServiceProvider;
use MacPaw\LaravelCrowdinIntegration\Commands\AddFile;
use MacPaw\LaravelCrowdinIntegration\Commands\Build;
use MacPaw\LaravelCrowdinIntegration\Commands\DownloadAll;
use MacPaw\LaravelCrowdinIntegration\Commands\UpdateFile;
use MacPaw\LaravelCrowdinIntegration\Commands\Upload;

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
        $this->publishes(
            [
                __DIR__ . '/../config/crowdin.php' => config_path('crowdin.php'),
            ]
        );

        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    AddFile::class,
                    DownloadAll::class,
                    UpdateFile::class,
                    Upload::class,
                    Build::class,
                ]
            );
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