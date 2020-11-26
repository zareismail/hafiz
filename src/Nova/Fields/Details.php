<?php

namespace Zareismail\Hafiz\Nova\Fields; 

use Illuminate\Support\Str;
use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Resource;
use Zareismail\Fields\Complex;
use Zareismail\Details\Models\DetailGroup;
use Zareismail\Hafiz\Nova\Places;

class Details extends MergeValue
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
		return 	$this->groups()->filter(function($group) {
			return Places::isShownOn($this->resource, $group) && $group->details->isNotEmpty();
		})->map([$this, 'mapIntoComplexField'])->filter()->values();
	}

	/**
	 * Get the avaialbel detail groups.
	 * 
	 * @return \Illuminate\Support\Collection
	 */
	public function groups()
	{
		return DetailGroup::with('details')->get();
	}

	public function mapIntoComplexField(DetailGroup $group)
	{
		if($fields = $group->details->fields($this->resource)->values()->all()) {
            return Complex::make($group->name, function() use ($fields) {
                return collect($fields)->each->resolveUsing([$this, 'resolveUsing']);
            })
            ->expansionOverflow(intval(Places::option('expansion_overflow', 2)))
            ->groupOverflow(intval(Places::option('group_overflow', 2)));  

        }
	}

	/**
	 * Fields resolve callback.
	 * 
	 * @param  mixed $value     
	 * @param  mixed $resource  
	 * @param  string $attribute 
	 * @return mixed            
	 */
	public function resolveUsing($value, $resource, $attribute)
	{
		$id = intval(Str::after($attribute, '->'));

        return $this->getValue(data_get(optional($this->resource->details)->find($id), 'pivot.value'));
	}

	/**
	 * Get the value.
	 * 
	 * @param  mixed $value 
	 * @return mixed        
	 */
	public function getValue($value = null)
	{
		if(empty($value) && ! is_numeric($value)) return null;

        $array = json_decode($value, true);

        if(is_array($array)) return $array;

        if(! is_numeric($value)) return $value; 
        
        return floatval($value) === intval($value)? intval($value) : floatval($value);
	}
}