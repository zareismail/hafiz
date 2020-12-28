<?php

namespace Zareismail\Hafiz\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Zareismail\NovaContracts\Auth\Authorizable;  
use Zareismail\NovaContracts\Auth\Authorization;

class AuthorizableModel extends Model implements Authorizable
{
    use HasFactory, SoftDeletes, Authorization;

	/**
	 * Indicate Model Authenticatable.
	 * 
	 * @return mixed
	 */
	public function owner()
	{
		return $this->auth();
	}
}
