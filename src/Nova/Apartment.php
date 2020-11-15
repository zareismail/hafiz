<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Number, Trix, BelongsTo, HasManyThrough, DateTime};
use Zareismail\NovaContracts\Nova\User;
use Zareismail\Details\Nova\DetailGroup;
// use Zareismail\Fields\Complex as Tab;

class Apartment extends MoreDetailsResource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizApartment::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
    	return [
    		ID::make(), 

            Number::make(__('Code'), function() {
                return "<b>{$this->code}</b>";
            })->asHtml(),

            BelongsTo::make(__('User'), 'auth', User::class)
                ->withoutTrashed()
                ->default($request->user()->getKey())
                ->searchable(), 

            BelongsTo::make(__('Building'), 'building', Building::class)
                ->withoutTrashed()
                ->searchable()
                ->readonly($request->viaResource() === Complex::class),

            BelongsTo::make(__('Complex'), 'complex', Complex::class)
                ->exceptOnForms(),

            Number::make(__('Floor'), 'floor')
                ->required()
                ->rules('required')
                ->help(__('What is the floor number of your apartment?'))
                ->min(0)
                ->default(0), 

    		Number::make(__('Number'), 'number')
    			->required()
    			->rules('required', function($attribute, $value, $fail) { 
                    if(! $this->apartmentExists($value)) {
                        $fail(__('The apartment :number already exists.', ['number' => request('number')])); 
                    }
                })
    			->help(__('What is the number of your apartment?'))
                ->min(0)
                ->default(0),

    		Trix::make(__('Description'), 'description') 
    			->help(__('Write about your apartment and their features.'))
    			->withFiles('public'), 

            // with(Tab::make(__('Details'), 'details'), function($complex) {
            //     DetailGroup::newModel()->with('details')->get()->each(function($group) use ($complex) {
            //         $complex->group($group->name, function() use ($group) {
            //             return $group->details->map(function($detail) {
            //                 return Number::make($detail->name);
            //             })->all();
            //         });
            //     });

            //     return $complex->help(__('A simple help description.'));
            // }),
    	];
    }

    public function apartmentExists(int $value = null)
    {
        return static::newModel()->whereHas('building', function($query) {
                    $query->whereKey(request('building'));
                })
                ->whereFloor(request('floor'))
                ->whereNumber($value)
                ->whereKeyNot($this->id)
                ->count() == 0;
        }
}