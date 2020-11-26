<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text, Slug, Trix, BelongsTo, HasMany, MorphMany};
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
    	return [
    		ID::make(), 

            BelongsTo::make(__('Complex'), 'complex', Complex::class)
                ->showCreateRelationButton()
                ->withoutTrashed()
                ->searchable()
                ->nullable(),

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

            new Fields\Details($this),

            Medialibrary::make(__('Gallery'), 'gallery')
                ->attachExisting()
                ->autouploading(), 

            HasMany::make(__('Apartments'), 'apartments', Apartment::class),

            HasMany::make(__('Common Areas'), 'areas', CommonArea::class),

            MorphMany::make(__('Costs'), 'costs', Cost::class),
    	];
    }
}