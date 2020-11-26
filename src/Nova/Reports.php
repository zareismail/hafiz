<?php

namespace Zareismail\Hafiz\Nova; 

abstract class Reports extends Resource
{ 
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Reports';  

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = []; 
}