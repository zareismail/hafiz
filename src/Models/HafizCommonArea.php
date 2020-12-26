<?php

namespace Zareismail\Hafiz\Models;  

use Zareismail\Fields\Contracts\Cascade;

class HafizCommonArea extends Model implements Cascade
{    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [ 
    	'floor' => 'integer', 
    ];  

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
        return $this->contracts()->inProgress()->with('auth')->get()->flatMap->auth;
    }
}
