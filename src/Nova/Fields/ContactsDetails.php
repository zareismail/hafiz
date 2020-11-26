<?php

namespace Zareismail\Hafiz\Nova\Fields; 
 
use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Resource; 
use Laravel\Nova\Fields\{Text, Textarea}; 
use GeneaLabs\NovaMapMarkerField\MapMarker;
use NovaItemsField\Items;

class ContactsDetails extends MergeValue
{ 
	/**
	 * The resource instance.
	 * 
	 * @var \Laravel\Nova\Resource
	 */
	public $resource;

    /**
     * Create new merge value instance.
     *
     * @param  \Illuminate\Support\Collection|\JsonSerializable|array  $data
     * @return void
     */
    public function __construct(Resource $resource)
    { 
    	$this->resource = $resource;

        parent::__construct($this->fields()); 
	}

	public function fields()
	{
		return [ 
            MapMarker::make('Google Location', 'google_location')
                ->defaultZoom(18)
                ->latitude('latitude')
                ->longitude('longitude')
                ->defaultLatitude(41.823611)
                ->defaultLongitude(-71.422222) 
                ->centerCircle(50, 'red', 1, .5)
                ->hideFromIndex(),

            Textarea::make(__('Address'), 'config->address') 
                ->help(__('Place full address detail.'))
                ->rules('required', 'max:500')
                ->hideFromIndex()
                ->required() 
                ->rows(2), 

            Text::make(__('Zipcode'), 'config->zipcode')
                ->hideFromIndex(),

            Text::make(__('Fax'), 'config->fax')
                ->hideFromIndex(), 

            Items::make(__('Phones'), 'config->phones')
                ->hideFromIndex(),
        ];
	} 
}