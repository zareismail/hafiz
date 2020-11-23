<?php

namespace Zareismail\Hafiz\Models;  


class HafizCommonArea extends Model
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
}
