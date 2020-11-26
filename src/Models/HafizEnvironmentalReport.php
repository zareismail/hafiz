<?php

namespace Zareismail\Hafiz\Models;  

use Zareismail\Contracts\Concerns\InteractsWithDetails;
use Zareismail\NovaContracts\Concerns\ShareableResource;
use Zareismail\NovaContracts\Models\AuthorizableModel;

class HafizEnvironmentalReport extends AuthorizableModel
{     
    use InteractsWithDetails;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    	'target_date' => 'datetime',
    ];

	/**
	 * Query the related Environmental`s.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function environmental()
	{ 
		return $this->belongsTo(HafizEnvironmental::class);
	} 

	/**
	 * Query the related reportables.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function reportable()
	{ 
		return $this->morphTo();
	} 
}
