<?php

namespace Zareismail\Hafiz\Models;  

class HafizBuilding extends Model
{  
	/**
	 * Query the related complex.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function complex()
	{ 
		return $this->belongsTo(HafizComplex::class);
	}

	/**
	 * Query the related apartments.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function apartments()
	{ 
		return $this->hasMany(HafizApartment::class, 'building_id');
	}
}
