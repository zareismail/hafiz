<?php

namespace Zareismail\Hafiz;
 
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Nova\Nova as LaravelNova; 

class HafizServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Models\HafizComplex::class => Policies\Complex::class,
        Models\HafizBuilding::class => Policies\Building::class,
        Models\HafizApartment::class => Policies\Apartment::class,
        Models\HafizCommonArea::class => Policies\CommonArea::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {  
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations'); 
        LaravelNova::serving([$this, 'servingNova']);
        $this->registerPolicies();
    } 

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    } 

    /**
     * Register any Nova services.
     *
     * @return void
     */
    public function servingNova()
    {
        LaravelNova::resources([
            Nova\Complex::class,
            Nova\Building::class,
            Nova\Apartment::class,
            Nova\CommonArea::class,
        ]);
    }
}
