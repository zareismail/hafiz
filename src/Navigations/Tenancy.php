<?php

namespace Zareismail\Hafiz\Navigations;
 

class Tenancy extends PreviousTenancy 
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
        return array_merge(parent::params(), [
            'resourceId' => 1
        ]);
    }
}
