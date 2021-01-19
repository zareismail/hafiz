<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Slug, Number, Trix, BelongsTo, HasMany, MorphMany};
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
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
    public static $with = ['costs', 'details', 'percapitas.resource.unit'];

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
                // ->showCreateRelationButton()
                ->withoutTrashed()
                ->searchable()
                ->nullable(),

            CascadeTo::make(__('Zone'), 'zone', Zone::class)
                // ->showCreateRelationButton()
                ->withoutTrashed()
                // ->searchable()
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

            // new Fields\Costs($this), 

            new Fields\Details($this),

            Medialibrary::make(__('Gallery'), 'gallery')
                ->attachExisting()
                ->autouploading(), 

            Panel::make(__('Contacts Details'), $this->filter([
                new Fields\ContactsDetails($this)
            ])),

            HasMany::make(__('Apartments'), 'apartments', Apartment::class),

            HasMany::make(__('Common Areas'), 'areas', CommonArea::class),

            // MorphMany::make(__('Costs'), 'costs', Cost::class),
    	];
    } 
}