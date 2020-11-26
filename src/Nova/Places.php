<?php

namespace Zareismail\Hafiz\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{Number, BooleanGroup};  
use Armincms\Bios\Resource;
use Zareismail\Details\Models\DetailGroup;

class Places extends Resource
{ 
    /**
     * The option storage driver name.
     *
     * @var string
     */
    public static $store = '';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Number::make(__('Group Overflow'), 'group_overflow') 
                ->help(__('Determine the minimum required fields for grouping in forms.'))
                ->withMeta([
                    'value' => static::option('overflow', 2)
                ]),

            Number::make(__('Expansion Overflow'), 'expansion_overflow') 
                ->help(__('Determine the minimum required fields for grouping in index.'))
                ->withMeta([
                    'value' => static::option('overflow', 2)
                ]),

            BooleanGroup::make(Apartment::label(), 'shown_on_'. Apartment::uriKey())
                ->options($groups = DetailGroup::get()->pluck('name', 'id'))
                ->help(__('Which details are allowed to display in the apartments index?')),

            BooleanGroup::make(CommonArea::label(), 'shown_on_'. CommonArea::uriKey())
                ->options($groups)
                ->help(__('Which details are allowed to display in the common areas index?')),

            BooleanGroup::make(Building::label(), 'shown_on_'. Building::uriKey())
                ->options($groups)
                ->help(__('Which details are allowed to display in the building index?')),

            BooleanGroup::make(Complex::label(), 'shown_on_'. Complex::uriKey())
                ->options($groups)
                ->help(__('Which details are allowed to display in the complex index?')),
        ];
    }

    public static function isShownOn($resource, DetailGroup $group)
    {
        return boolval(data_get((array) static::option('shown_on_'. $resource::uriKey()), $group->id)); 
    }
}
