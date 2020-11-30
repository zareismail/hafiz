<?php

namespace Zareismail\Hafiz\Nova; 

abstract class Communication extends Resource
{ 
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Communications';  

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = []; 
}