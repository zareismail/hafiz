<?php 

namespace Zareismail\Hafiz\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

interface Reportable
{
	/**
	 * Query the related Environmentals.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
	 */
	public function reports(): HasOneOrMany;
}