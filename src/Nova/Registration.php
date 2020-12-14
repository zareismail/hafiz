<?php

namespace Zareismail\Hafiz\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;   
use Zareismail\NovaContracts\Nova\BiosResource;
use Zareismail\NovaPolicy\PolicyRole;

class Registration extends BiosResource
{ 
    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    { 
        return with(PolicyRole::get()->pluck('name', 'id'), function($roles) {
            return [  
                Select::make(__('Tenant Role'), static::prefix('tenant_role'))
                    ->options($roles)
                    ->required()
                    ->rules('required')
                    ->displayUsingLabels(),
            ];
        });
    }  
}
