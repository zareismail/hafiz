<?php

namespace Zareismail\Hafiz\Models;  

use Zareismail\Fields\Contracts\Cascade;

class HafizBuilding extends Model implements Cascade
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

	/**
	 * Query the related apartments.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function areas()
	{ 
		return $this->hasMany(HafizCommonArea::class, 'building_id');
	}
	/**
	 * Query the realted resource.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
	 */
	public function parent()
	{
		return $this->complex();
	}

	/**
	 * Get the subscribed users.
	 *  
	 * @return Null|\Illuminate\Database\Eloquent\Collection
	 */
	public function subscribers()
	{ 
		$apartments = $this->apartments->flatMap->subscribers();
		$areas = $this->areas->flatMap->subscribers();

		return $areas->merge($apartments)->unique(function($user) {
			return $user->getKey();
		});
	}
}
