<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Slug, Number, Trix, BelongsTo, HasMany, MorphMany}; 
use Zareismail\Fields\BelongsTo as CascadeTo;
use Zareismail\NovaContracts\Nova\User;
use Zareismail\NovaLocation\Nova\Zone;
use Zareismail\Costable\Nova\Cost; 

class Building extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizBuilding::class;

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['details', 'auth', 'media'];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'number'
    ];

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

            BelongsTo::make(__('User'), 'auth', User::class)
                ->withoutTrashed()
                ->default($request->user()->getKey())
                ->searchable()
                ->canSee(function($request) {
                    return $request->user()->can('addUser', static::newModel());
                }), 

            CascadeTo::make(__('Complex'), 'complex', Complex::class) 
                ->withoutTrashed()
                ->searchable()
                ->nullable(),

            CascadeTo::make(__('Zone'), 'zone', Zone::class) 
                ->withoutTrashed() 
                ->nullable()
                ->rules('required_without:complex'),

            Number::make(__('Number'), 'number')
                ->required()
                ->rules('required')
                ->help(__('What is the number of your building?')),

    		Text::make(__('Name'), 'name')
    			->required()
    			->rules('required')
    			->help(__('What is the name of your building?')),

    		Slug::make(__('Slug'), 'slug')
    			->from('name') 
    			->help(__('This is part of the URL. If you don\'t info about it, leave it blank.')),

    		Trix::make(__('Description'), 'description') 
    			->help(__('Write about your building and their features.'))
    			->withFiles('public'), 

            $this->when($request->isResourceDetailRequest() && $this->percapitas->isNotEmpty(), function() {
                return new Fields\PerCapitas($this->percapitas);
            }), 

            new Fields\Details($this),

            $this->mergeGalleryField(),

            Panel::make(__('Contacts Details'), $this->filter([
                new Fields\ContactsDetails($this)
            ])),

            HasMany::make(__('Apartments'), 'apartments', Apartment::class),

            HasMany::make(__('Common Areas'), 'areas', CommonArea::class),
 
    	];
    } 

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {  
        return parent::relatableQuery($request, $query) 
                    ->when(static::shouldAuthenticate($request), function($query) { 
                        $query->orWhereHas('apartments.contracts', function($query) {
                            $query->authenticate();
                        })
                        ->orWhereHas('areas.contracts', function($query) {
                            $query->authenticate();
                        });
                    });
    }   

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyContractSearch($query, $search)
    { 
        static::applyRelatedSearch($query, $search);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyPerCapitaSearch($query, $search)
    { 
        static::applyRelatedSearch($query, $search);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyCostSearch($query, $search)
    { 
        static::applyRelatedSearch($query, $search);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyRelatedSearch($query, $search)
    { 
        $query->orWhereHas('complex', function($query) use ($search) {
            $query->where('name', 'like', '%'.$search.'%'); 
        });
    }

    /**
     * Get the cards available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {  
        return array_merge([
            Metrics\CreatedBuildings::make(),
        ], parent::cards($request));  
    }

    /**
     * Get the filters available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            Filters\Complex::make(), 
        ];
    }
}
