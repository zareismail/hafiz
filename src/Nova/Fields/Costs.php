<?php

namespace Zareismail\Hafiz\Nova\Fields; 
 
use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Resource; 
use Laravel\Nova\Fields\{Currency}; 
use Zareismail\Costable\Models\CostableFee; 

class Costs extends MergeValue
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
		return CostableFee::get()->forResource($this->resource)->map(function($fee) {
			return Currency::make($fee->name, "config->fees->{$fee->id}")
				 		->default(0.00)
						->required()
						->rules('required')
						->hideFromIndex()
						->help(__('Determine default :fee amount', [
							'fee' => $fee->name
						]));
		})->values();
	} 
}