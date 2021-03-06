<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Slug, Trix, BelongsTo, HasMany, MorphMany, HasManyThrough}; 
use Zareismail\Fields\BelongsTo as CascadeTo;
use Zareismail\NovaContracts\Nova\User;
use Zareismail\NovaLocation\Nova\Zone;
use Zareismail\Details\Nova\Detail;
use Zareismail\Costable\Nova\Cost; 

class Complex extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizComplex::class;

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['details', 'auth'];

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

            CascadeTo::make(__('Zone'), 'zone', Zone::class) 
                ->withoutTrashed(), 

            Text::make(__('Name'), 'name')
                ->required()
                ->rules('required')
                ->help(__('What is the name of your complex?')),

    		Slug::make(__('Slug'), 'slug')
    			->from('name') 
    			->help(__('This is part of the URL. If you don\'t have info about it, leave it blank.')), 

    		Trix::make(__('Description'), 'description') 
    			->help(__('Write about your complex and their features.'))
    			->withFiles('public'),  

            $this->when($request->isResourceDetailRequest() && $this->percapitas->isNotEmpty(), function() {
                return new Fields\PerCapitas($this->percapitas);
            }),  

            new Fields\Details($this),  

            $this->mergeGalleryField(),

            Panel::make(__('Contacts Details'), $this->filter([
                new Fields\ContactsDetails($this)
            ])),

            HasMany::make(__('Buildings'), 'buildings', Building::class), 
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
                    ->when(static::shouldAuthenticate($request), function($query) use ($request) {
                        $query->orWhereHas('buildings.contracts', function($query) {
                            $query->authenticate();
                        })
                        ->orWhereHas('buildings.apartments.contracts', function($query) {
                            $query->authenticate();
                        })
                        ->orWhereHas('buildings.areas.contracts', function($query) {
                            $query->authenticate();
                        });
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
            Metrics\CreatedComplexes::make(),
        ], parent::cards($request));  
    }
}
