<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text, Slug, Trix, HasMany, MorphMany, HasManyThrough};
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
    	return [
    		ID::make(),

    		Text::make(__('Name'), 'name')
    			->required()
    			->rules('required')
    			->help(__('What is the name of your complex?')),

    		Slug::make(__('Slug'), 'slug')
    			->from('name') 
    			->help(__('This is part of the URL. If you don\'t info about it, leave it blank.')), 

    		Trix::make(__('Description'), 'description') 
    			->help(__('Write about your complex and their features.'))
    			->withFiles('public'), 

            new Fields\Details($this),  
             
            Medialibrary::make(__('Gallery'), 'gallery')
                ->attachExisting()
                ->autouploading(), 

            HasMany::make(__('Buildings'), 'buildings', Building::class),

            HasManyThrough::make(__('Apartments'), 'apartments', Apartment::class),

            MorphMany::make(__('Costs'), 'costs', Cost::class),
            
            HasMany::make(__('Environmental Reports'), 'reports', EnvironmentalReport::class),
    	];
    }
}