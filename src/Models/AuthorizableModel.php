<?php

namespace Zareismail\Hafiz\Models;
 
use Zareismail\NovaContracts\Auth\Authorizable;  
use Zareismail\NovaContracts\Auth\Authorization;

class AuthorizableModel extends Model implements Authorizable
{
    use Authorization;

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
