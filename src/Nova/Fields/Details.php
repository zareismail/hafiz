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
	 * List of detail groups.
	 * 
	 * @var \Illuminate\Database\Eloquent\Collection
	 */
	public static $cachedDetailGroups;

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
		$availableFields = $this->groups()->filter(function($group) {
			return $group->details->isNotEmpty();
		}); 

		return request()->route('field', false) 
				? $availableFields->flatMap([$this, 'availableFields'])->filter()->values()
				: $availableFields->map([$this, 'mapIntoComplexField'])->filter()->values();
	}

	/**
	 * Get the avaialbe detail groups.
	 * 
	 * @return \Illuminate\Support\Collection
	 */
	public function groups()
	{
		if(! isset(static::$cachedDetailGroups)) {
			static::$cachedDetailGroups = DetailGroup::with('details')->whereKey((array) Places::option('shown_on_'.$this->resource::uriKey()))->get();
		}

		return static::$cachedDetailGroups;
	}

	public function availableFields(DetailGroup $group)
	{
		return $group->details->fields($this->resource)->values()->all();
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