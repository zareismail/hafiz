<?php

namespace Zareismail\Hafiz\Models; 


class HafizInsurance extends Report 
{  
	/**
	 * Return the scope group.
	 * 
	 * @return string
	 */
    public static function resourceScope(): string
    {
    	return \Zareismail\Hafiz\Nova\Insurance::class;
    }
}
