<?php

namespace Zareismail\Hafiz\Contracts;

interface Subscribable
{
	/**
	 * Get the subscribed users.
	 *  
	 * @return Null|\Illuminate\Database\Eloquent\Collection
	 */
	public function subscribers();
}