<?php

namespace Zareismail\Hafiz\Navigations; 
 
use Zareismail\QuickTheme\Navigation; 
use Zareismail\Hafiz\Models\HafizApartment; 

class Apartment extends PreviousApartments 
{      
    /**
     * Get the router name.
     *
     * @return string
     */
    public static function name()
    {
        return 'detail';
    }

    /**
     * Get the routers.
     *
     * @return string
     */
    public static function params(): array
    {
        $contracts = request()->user()->load('contracts.contractable')->contracts->filter(function($contract) {
            return $contract->contractable_type === HafizApartment::class;
        }); 

        return array_merge(parent::params(), [
            'resourceId' => optional($contracts->pop())->contractable_id,
        ]);
    }
}
