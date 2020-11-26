<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request; 
use Laravel\Nova\Fields\{ID, Number, Trix, BelongsTo, HasMany, MorphMany, HasManyThrough, DateTime};
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Zareismail\NovaContracts\Nova\User;
use Zareismail\Costable\Nova\Cost;

class Apartment extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizApartment::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'number';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'number'
    ];

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['details', 'building'];

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
            })->asHtml()->exceptOnForms(),

            BelongsTo::make(__('User'), 'auth', User::class)
                ->withoutTrashed()
                ->default($request->user()->getKey())
                ->searchable()
                ->canSee(function($request) {
                    return $request->user()->can('update', Building::newModel());
                }), 

            BelongsTo::make(__('Building'), 'building', Building::class)
                ->withoutTrashed()
                ->searchable()
                ->readonly($request->viaResource() === Complex::class),

            BelongsTo::make(__('Complex'), 'complex', Complex::class)
                ->exceptOnForms()
                ->showCreateRelationButton(),

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

            new Fields\Costs($this), 

            new Fields\Details($this),  

            Medialibrary::make(__('Gallery'), 'gallery')
                ->attachExisting()
                ->autouploading()
                ->attachExisting(function ($query, $request, $model) {
                    $query->authenticate();
                }),

            MorphMany::make(__('Costs'), 'costs', Cost::class),

            HasMany::make(__('Environmental Reports'), 'reports', EnvironmentalReport::class),
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

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    { 
        return forward_static_call([new Building($this->building), 'title']).': '.$this->number;
    }
}