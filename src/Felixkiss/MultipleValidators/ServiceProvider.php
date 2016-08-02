<?php namespace Felixkiss\MultipleValidators;

use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Replace the validation factory. The custom factory allows multiple
        // validation resolvers to be registered at once.
        $oldFactory = $this->app['validator'];
        $this->app->singleton('validator', function($app) use ($oldFactory) {
            $factory = $app->make(Factory::class);
            $factory->addFactory($oldFactory);
            $factory->setPresenceVerifier($oldFactory->getPresenceVerifier());
            return $factory;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('validator');
    }
}
