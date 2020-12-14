<?php

namespace Zareismail\Hafiz\Navigations;
 

class Maturity extends Payments 
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
    public static function params(): array
    {
        return array_merge(parent::params());
    }
}
