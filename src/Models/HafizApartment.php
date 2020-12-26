<?php

namespace Zareismail\Hafiz\Models;  
    
use Znck\Eloquent\Traits\BelongsToThrough;
use Zareismail\Fields\Contracts\Cascade;

class HafizApartment extends AuthorizableModel implements Cascade
{   
	use BelongsToThrough;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    	'code'	=> 'integer',
    	'floor' => 'integer',
    	'number'=> 'integer',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function($model) { 
        	$model->when(is_null($model->code), function() use ($model) {
        		$model->fillCode();
        	});
        });
    }

    /**
     * Fill the model with a new code.
     * 
     * @return $this
     */
    public function fillCode()
    {
    	return $this->forceFill([
    		'code' => $this->generateCode(),
    	]); 
    }

    /**
     * Generate unique code.
     * 
     * @return integer
     */
    public function generateCode()
    {
    	while (static::whereCode($code = rand(9999999, 99999999))->whereKeyNot($this->key)->count());

    	return $code;
    }

	/**
	 * Query the related building.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function building()
	{ 
		return $this->belongsTo(HafizBuilding::class);
	}

	/**
	 * Query the related complex.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
	 */
	public function complex()
	{ 
		return $this->belongsToThrough(HafizComplex::class, HafizBuilding::class, null, '', [
			HafizComplex::class => 'complex_id', 
			HafizBuilding::class=> 'building_id' 
		]);
	}

    /**
     * Query the realted resource.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
     */
    public function parent()
    {
        return $this->building();
    }

    /**
     * Get the subscribed users.
     *  
     * @return Null|\Illuminate\Database\Eloquent\Collection
     */
    public function subscribers()
    { 
        return $this->contracts()->inProgress()->with('auth')->get()->map->auth->push($this->auth);
    }
}
