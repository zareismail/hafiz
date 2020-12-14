<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;  
use Laravel\Nova\Fields\{ID, Text, Number, Avatar};
use Zareismail\NovaContracts\Nova\User;

class Profile extends User
{    
    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Avatar::make(__('Image'), 'profile->image')
                ->rounded()
                ->hideFromDetail(boolval($request->get('card') == 'profile')),

            Text::make(__('Username'), 'name'),

            Text::make(__('Firstname'), 'profile->firstname'),

            Text::make(__('Lastname'), 'profile->lastname'),

            Text::make(__('Mobile'), 'mobile'),

            Number::make(__('Age'), 'profile->age'),
        ];
    }
}