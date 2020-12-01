<?php

namespace Zareismail\Hafiz\Models; 


class HafizAccount extends Report 
{  
	/**
	 * Return the scope group.
	 * 
	 * @return string
	 */
    public static function resourceScope(): string
    {
    	return \Zareismail\Hafiz\Nova\Account::class;
    }
}
