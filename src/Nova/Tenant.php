<?php

namespace Zareismail\Hafiz\Nova; 

use Laravel\Nova\Http\Requests\NovaRequest;
use Zareismail\NovaContracts\Nova\User;
use Zareismail\Hafiz\Helper;

class Tenant extends User
{       
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Users';  

    /**
     * Return the location to redirect the user after update.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return tap(parent::redirectAfterUpdate($request, $resource), function() use ($resource) {
            Helper::ensureIsTenant($resource->resource); 
        });
    }

    /**
     * Return the location to redirect the user after creation.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return tap(parent::redirectAfterCreate($request, $resource), function() use ($resource) {
            Helper::ensureIsTenant($resource->resource); 
        });
    }
    
    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->whereHas('roles', function($query) {
            return $query->whereKey(intval(Registration::option('tenant_role')));
        });
    }
}