<?php namespace Spekkionu\Assetcachebuster;

use Illuminate\Support\ServiceProvider;
use Spekkionu\Assetcachebuster\HashReplacer\ConfigHashReplacer;
use Spekkionu\Assetcachebuster\Writer\ConfigWriter;
use Illuminate\Contracts\Foundation\Application;

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
        $this->publishes([
            dirname(dirname(__DIR__)) . '/config/assetcachebuster.php' => config_path('assetcachebuster.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            dirname(dirname(__DIR__)) . '/config/assetcachebuster.php', 'assetcachebuster'
        );

        $this->app['assetcachebuster'] = $this->app->share(function (Application $app) {
            $options['enable'] = $app['config']->get('assetcachebuster.enable');
            $options['hash'] = $app['config']->get('assetcachebuster.hash');
            $options['cdn'] = $app['config']->get('assetcachebuster.cdn');
            $options['prefix'] = $app['config']->get('assetcachebuster.prefix');
            return new Assetcachebuster($options);
        });

        $this->app->bind('Spekkionu\Assetcachebuster\Writer\ConfigWriter', function(Application $app){
            return new ConfigWriter($app->make('Illuminate\Filesystem\Filesystem'), $app->make('path.config'));
        });

        $this->app->bind('Spekkionu\Assetcachebuster\Writer\WriterInterface', 'Spekkionu\Assetcachebuster\Writer\ConfigWriter');

        $this->app->bind('Spekkionu\Assetcachebuster\HashReplacer\ConfigHashReplacer', function(Application $app){
            return new ConfigHashReplacer($app->make('assetcachebuster'), $app->make('Spekkionu\Assetcachebuster\Writer\WriterInterface'));
        });

        $this->app->bind('Spekkionu\Assetcachebuster\HashReplacer\HashReplacerInterface', 'Spekkionu\Assetcachebuster\HashReplacer\ConfigHashReplacer');

        // Register artisan command
        $this->app['command.assetcachebuster.generate'] = $this->app->share(
            function (Application $app) {
                return new Console\GenerateCommand($app->make('Spekkionu\Assetcachebuster\HashReplacer\HashReplacerInterface'), $app->make('Illuminate\Contracts\Config\Repository'));
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
        return array(
            'assetcachebuster',
            'command.assetcachebuster.generate',
            'Spekkionu\Assetcachebuster\Writer\ConfigWriter',
            'Spekkionu\Assetcachebuster\Writer\WriterInterface',
            'Spekkionu\Assetcachebuster\HashReplacer\ConfigHashReplacer',
            'Spekkionu\Assetcachebuster\HashReplacer\HashReplacerInterface',
        );
    }
}
