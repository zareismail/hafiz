<?php

namespace Zareismail\Hafiz\Navigations;

use Zareismail\QuickTheme\Navigation;
 

class CurrentContract extends ListContracts 
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
            'resourceName' => 'contracts',
            'resourceId' => 1,
        ]);
    }
}
