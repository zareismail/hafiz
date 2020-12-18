<?php

namespace Zareismail\Hafiz\Nova; 

use Laravel\Nova\Http\Requests\NovaRequest;
use Zareismail\NovaContracts\Nova\User as Resource; 

class User extends Resource
{       
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\User::class;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Users';   
}