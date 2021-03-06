<?php

namespace Zareismail\Hafiz\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{Number, BooleanGroup};   
use Zareismail\Details\Models\DetailGroup;
use Zareismail\NovaContracts\Nova\BiosResource;

class Places extends BiosResource
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
            Number::make(__('Group Overflow'), static::prefix('group_overflow')) 
                ->help(__('Determine the minimum required fields for grouping in forms.'))
                ->withMeta([
                    'value' => static::option('overflow', 2)
                ]),

            Number::make(__('Expansion Overflow'), static::prefix('expansion_overflow')) 
                ->help(__('Determine the minimum required fields for grouping in index.'))
                ->withMeta([
                    'value' => static::option('overflow', 2)
                ]),

            BooleanGroup::make(Apartment::label(), static::prefix('shown_on_'. Apartment::uriKey()))
                ->options($groups = DetailGroup::get()->pluck('name', 'id'))
                ->help(__('Which details are allowed to display in the apartments index?')),

            BooleanGroup::make(CommonArea::label(), static::prefix('shown_on_'. CommonArea::uriKey()))
                ->options($groups)
                ->help(__('Which details are allowed to display in the common areas index?')),

            BooleanGroup::make(Building::label(), static::prefix('shown_on_'. Building::uriKey()))
                ->options($groups)
                ->help(__('Which details are allowed to display in the building index?')),

            BooleanGroup::make(Complex::label(), static::prefix('shown_on_'. Complex::uriKey()))
                ->options($groups)
                ->help(__('Which details are allowed to display in the complex index?')),
        ];
    }

    public static function isShownOn($resource, DetailGroup $group)
    {
        return boolval(data_get((array) static::option('shown_on_'. $resource::uriKey()), $group->id)); 
    }
}
