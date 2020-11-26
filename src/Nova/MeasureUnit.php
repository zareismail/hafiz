<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text};
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;

class MeasureUnit extends Reports
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizMeasureUnit::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'label';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'label', 'symbol'
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

    		Text::make(__('Unit Label'), 'label')
    			->required()
    			->rules('required')
    			->help(__('Choose a label for the measuring unit.')),

            Text::make(__('Unit Symbol'), 'symbol')
                ->required()
                ->rules('required')
                ->help(__('Choose a symbol for the measuring unit.')),
    	];
    }
}