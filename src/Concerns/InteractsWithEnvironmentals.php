<?php

namespace Zareismail\Hafiz\Concerns; 

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Zareismail\Hafiz\Models\HafizEnvironmentalReport;

trait InteractsWithEnvironmentals
{ 
	/**
	 * Query the related Environmentals.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
	 */
	public function reports(): HasOneOrMany
	{
		return $this->morphMany(HafizEnvironmentalReport::class, 'reportable');
	}
} 