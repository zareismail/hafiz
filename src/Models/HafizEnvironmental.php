<?php

namespace Zareismail\Hafiz\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Zareismail\Contracts\Concerns\InteractsWithConfigs;
use Zareismail\NovaContracts\Concerns\ShareableResource;

class HafizEnvironmental extends Model
{    
	use SoftDeletes, HasFactory, ShareableResource, InteractsWithConfigs;

	/**
	 * Query the related MeasureUnit`s.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function unit()
	{ 
		return $this->belongsTo(HafizMeasureUnit::class, 'unit_id');
	}

    /**
     * Get the sharing contracts interface.
     *  
     * @return string            
     */
    public static function sharingContract(): string
    {
    	return \Zareismail\Hafiz\Contracts\Reportable::class;
    } 

    /**
     * Determine share condition.
     * 
     * @param  \Laravel\Nova\Resource $resource
     * @param  string $condition 
     * @return bool            
     */
    public function sharedAs($resource, string $condition): bool
    {
        return boolval($this->getConfig($condition.'.'.$resource::uriKey()));
    } 
}
