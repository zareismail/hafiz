<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request; 
use Laravel\Nova\Fields\{ID, Text, Number, Trix, BelongsTo};
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary; 

class CommonArea extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizCommonArea::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'number'
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

            BelongsTo::make(__('Building'), 'building', Building::class)
                ->withoutTrashed()
                ->searchable(), 

            Text::make(__('Name'), 'name')
                ->required()
                ->rules('required')
                ->help(__('What is the name of the area?')),

            Number::make(__('Floor'), 'floor')
                ->required()
                ->rules('required')
                ->help(__('What is the floor number of the area?')) 
                ->default(0), 

    		Trix::make(__('What is its use?'), 'explanation') 
    			->help(__('Write about this area and its uses.')), 

            new Fields\Details($this),  

            Medialibrary::make(__('Gallery'), 'gallery')
                ->attachExisting()
                ->autouploading()
                ->attachExisting(function ($query, $request, $model) {
                    $query->authenticate();
                }),
    	];
    }
}