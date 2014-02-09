<?php namespace Spekkionu\Assetcachebuster;

use Illuminate\Support\ServiceProvider;

class AssetcachebusterServiceProvider extends ServiceProvider
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
     */
    public function boot()
    {
        $this->package('spekkionu/assetcachebuster');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['assetcachebuster'] = $this->app->share(function ($app) {
            $options['enable'] = $this->app['config']->get('assetcachebuster::enable');
            $options['hash'] = $this->app['config']->get('assetcachebuster::hash');
            $options['cdn'] = $this->app['config']->get('assetcachebuster::cdn');
            $options['prefix'] = $this->app['config']->get('assetcachebuster::prefix');
            return new Assetcachebuster($options);
        });

        // Register artisan command
        $this->app['command.assetcachebuster.generate'] = $this->app->share(
            function ($app) {
                return new Console\GenerateCommand($app['files']);
            }
        );
        $this->commands(
            'command.assetcachebuster.generate'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('assetcachebuster', 'command.assetcachebuster.generate');
    }
}
