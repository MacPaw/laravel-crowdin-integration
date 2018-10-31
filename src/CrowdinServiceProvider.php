<?php

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
        if ( function_exists( "config_path" ) )
        {
            $this->publishes( array(
                __DIR__ . '/../config/crowdin.php' => config_path( 'crowdin.php' ) ,
            ) );
        }
        if ($this->app->runningInConsole()) {
            $this->commands([
                \MacPaw\LaravelCrowdinIntegration\Crowdin\AddFile::class,
                \MacPaw\LaravelCrowdinIntegration\Crowdin\DownloadAll::class,
                \MacPaw\LaravelCrowdinIntegration\Crowdin\UpdateFile::class,
                \MacPaw\LaravelCrowdinIntegration\Crowdin\Upload::class,
            ]);
        }
    }
}