<?php namespace Spekkionu\Assetcachebuster;

use Illuminate\Support\ServiceProvider;

class AssetcachebusterServiceProvider extends ServiceProvider {

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
		$this->app['assetcachebuster'] = $this->app->share(function($app) {
			$hash = $this->app['config']->get('assetcachebuster::hash');
			$cdn = $this->app['config']->get('assetcachebuster::cdn');
			$prefix = $this->app['config']->get('assetcachebuster::prefix');
            return new Assetcachebuster($hash, $prefix, $cdn);
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