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
        Models\HafizAccount::class => Policies\Account::class,
        Models\HafizComplex::class => Policies\Complex::class,
        Models\HafizBuilding::class => Policies\Building::class,
        Models\HafizApartment::class => Policies\Apartment::class,
        Models\HafizInsurance::class => Policies\Insurance::class,
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'hafiz');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations'); 
        LaravelNova::serving([$this, 'servingNova']); 
        DynamicRelationship::register();
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
            Nova\Account::class,
            Nova\Profile::class,
            Nova\Tenant::class,
            Nova\Insurance::class,
            Nova\Registration::class,
            Nova\Places::class,
            Nova\Complex::class,
            Nova\Building::class,
            Nova\Apartment::class,
            Nova\CommonArea::class, 
        ]); 

        LaravelNova::tools([
            \Zareismail\QuickTheme\QuickTheme::cards([
                tap(\Zareismail\Cards\Profile::make(), function($profile) {
                    $profile->resourceUsing(Nova\Profile::class)->avatarUsing(function($user) {
                        if($path = data_get($user, 'profile.image')) {
                            return \Storage::disk('public')->url($path);
                        } 
                    });
                }),
            ])
            ->navigations([
                Navigations\Tenancy::class,
                Navigations\PreviousTenancy::class,
                Navigations\Maturity::class,
                Navigations\Payments::class,
                Navigations\CurrentContract::class,
                Navigations\ListContracts::class,
                Navigations\ReportProblem::class,
                Navigations\Issues::class,
                Navigations\SendToOwner::class,
                Navigations\Letters::class,
                Navigations\ReportEnvironmental::class,
                Navigations\EnvironmentalReports::class,
            ])
            ->canSee(function($request) { 
                return Helper::isTenant($request->user());
            }),
        ]);
    } 
}
