<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text, Slug, Trix, BelongsTo, HasMany}; 
use Zareismail\NovaContracts\Nova\Fields\SharedResources;
use Superlatif\NovaTagInput\Tags;

class Environmental extends Reports
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizEnvironmental::class; 

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

            BelongsTo::make(__('Measuring Unit'), 'unit', MeasureUnit::class)
                ->showCreateRelationButton()
                ->withoutTrashed()
                ->searchable(),

    		Text::make(__('Name'), 'name')
    			->required()
    			->rules('required')
    			->help(__('What is the type of environmental report?')),

            Tags::make(__('Consumption Uses'), 'config->options')
                ->help(__('Force the user to separate the consumption cases.'))
                ->hideFromIndex(),

            new SharedResources($request, $this),
    	];
    }
}