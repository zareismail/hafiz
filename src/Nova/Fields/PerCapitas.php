<?php

namespace Zareismail\Hafiz\Nova\Fields; 
 
use Illuminate\Http\Resources\MergeValue; 
use Laravel\Nova\Fields\{Heading, Number}; 
use Zareismail\Shaghool\Helper; 

class PerCapitas extends MergeValue
{ 
	/**
	 * The resource instance.
	 * 
	 * @var \Laravel\Nova\Resource
	 */
	public $capitas;

    /**
     * Create new merge value instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection $capitas
     * @return void
     */
    public function __construct($capitas)
    { 
    	$this->capitas = $capitas;

        parent::__construct(array_merge([
        	Heading::make(__('Per Capita'))
        ], $this->preapareFields())); 
	}

	public function preapareFields()
	{
		return $this->capitas->map(function($capita) {
	        return Number::make(optional($capita->resource)->name, function() use ($capita) {
	            return 	data_get(Helper::periods(), $capita->period).' - '.
	            		$capita->balance.PHP_EOL.
	            		data_get($capita->resource, 'unit.name');
	        });
	    })->all(); 
	} 
}