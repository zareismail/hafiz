<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text, Slug, Trix, HasMany, HasManyThrough};
use Zareismail\Details\Nova\Detail;

class Complex extends MoreDetailsResource
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

            Feilds\Details::make(__('Features'), 'details', Detail::class),

    		Trix::make(__('Description'), 'description') 
    			->help(__('Write about your complex and their features.'))
    			->withFiles('public'), 

            HasMany::make(__('Buildings'), 'buildings', Building::class),

            HasManyThrough::make(__('Apartments'), 'apartments', Apartment::class),
    	];
    }
}