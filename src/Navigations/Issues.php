<?php

namespace Zareismail\Hafiz\Navigations; 
 
use Zareismail\QuickTheme\Navigation;

class Issues extends Navigation 
{     
    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    { 
        return (string) view('hafiz::icons.maintenance');
    } 

    /**
     * Get the router name.
     *
     * @return string
     */
    public static function name()
    {
        return 'index';
    }

    /**
     * Get the routers.
     *
     * @return string
     */
    public static function params(): array
    {
        return [
            'resourceName' => 'maintenance-issues',
        ];
    }
}
