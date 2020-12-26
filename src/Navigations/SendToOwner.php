<?php

namespace Zareismail\Hafiz\Navigations; 

use Zareismail\Hafiz\Nova\Landlord;

class SendToOwner extends Letters 
{      
    /**
     * Get the router name.
     *
     * @return string
     */
    public static function name()
    {
        return 'create';
    } 

    /**
     * Get the routers.
     *
     * @return string
     */
    public static function query(): array
    {
        $tenants = request()->user()->load('contracts.contractable')->contracts->filter(function($contract) {
            return $contract->contractable_type === HafizApartment::class;
        })->map->contractable; 

    	return array_merge(parent::query(), [
    		'viaResource' => Landlord::uriKey(),
    		'viaResourceId' => optional($tenants->pop())->auth_id,
    		'viaRelationship' => 'letters',
    	]);
    }
}
