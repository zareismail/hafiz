<?php

namespace Zareismail\Hafiz\Models;
 

class HafizComplex extends Model
{  
	/**
	 * Query the related buildings.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function buildings()
	{ 
		return $this->hasMany(HafizBuilding::class, 'complex_id');
	}

	/**
	 * Query the related apartments through the buildings.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
	 */
	public function apartments()
	{ 
		return $this->hasManyThrough(
			HafizApartment::class, HafizBuilding::class, 'complex_id', 'building_id' 
		);
	}
}
