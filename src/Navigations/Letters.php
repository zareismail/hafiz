<?php

namespace Zareismail\Hafiz\Navigations; 
 
use Zareismail\QuickTheme\Navigation;

class Letters extends Navigation 
{     
    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    { 
        return (string) view('hafiz::icons.mail');
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
            'resourceName' => 'letters',
        ];
    }
}