<?php

namespace Zareismail\Hafiz\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model as LaravelModel, SoftDeletes};
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Zareismail\Details\Concerns\InteractsWithDetails;  
use Zareismail\Details\Contracts\MoreDetails;
use Zareismail\Costable\Concerns\InteractsWithCosts;
use Zareismail\Costable\Contracts\Costable;
use Zareismail\Hafiz\Concerns\InteractsWithEnvironmentals;
use Zareismail\Hafiz\Contracts\Reportable;


class Model extends LaravelModel implements MoreDetails, HasMedia, Costable, Reportable
{
    use HasFactory, SoftDeletes, InteractsWithDetails, HasMediaTrait, InteractsWithCosts;
    use InteractsWithEnvironmentals;

    /**
     * The preapred details for sync.
     * 
     * @var array
     */
	protected $syncDetails = [];
 
    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
		parent::boot();

		static::saved(function($model) {
			$model->syncDetailsIfNotSynced(); 
		});
	}

	/**
	 * Sync the preapared details if not synced.
	 * 
	 * @return $this
	 */
	public function syncDetailsIfNotSynced()
	{ 
		empty($this->syncDetails) || $this->syncDetails();

		return $this;
	} 

	/**
	 * Sync the preapared details.
	 * 
	 * @return $this
	 */
	public function syncDetails()
	{ 
		$this->details()->sync($this->syncDetails);

		$this->syncDetails = []; 

		return $this;
	}

    /**
     * Set a given JSON attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function fillJsonAttribute($key, $value)
    { 
    	if($id = Str::after($key, 'detail->')) { 
    		if(! is_null($value = $this->castDetailsValue($value))) { 
    			$this->syncDetails[$id] = compact('value');
    		} 

    		return $this;
    	} 

    	return parent::fillJsonAttribute($key, $value);
    }	

	public function castDetailsValue($value = null)
	{ 
		if(empty($value) && ! is_numeric($value)) return null;

		if(is_array($value)) return json_encode($value);

		if(! is_numeric($value)) return strval($value);

		return floatval($value) !== intval($value) ? floatval($value) : intval($value);
	}  

	public function registerMediaCollections(): void
	{

	    $this
	        ->addMediaCollection('gallery')
	        ->registerMediaConversions(function ($media) {
	            $this
	                ->addMediaConversion('thumb')
	                ->width(100)
	                ->height(100);

	            $this
	                ->addMediaConversion('mid')
	                ->width(720)
	                ->height(480);

	            $this
	                ->addMediaConversion('larg')
	                ->width(1080)
	                ->height(720);
	        });
	}
}
