<?php

namespace Zareismail\Hafiz\Models;
 
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Zareismail\NovaContracts\Models\AuthorizableModel;
use Zareismail\Contracts\Concerns\InteractsWithDetails;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait; 


abstract class Report extends AuthorizableModel implements HasMedia
{
    use HasFactory, SoftDeletes, HasMediaTrait, InteractsWithDetails;   

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hafiz_reports';

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function($model) {
            $model->setAttribute('group', static::resourceScope()); 
        });

        static::addGlobalScope(function($model) {
            $model->whereGroup(static::resourceScope()); 
        });
    }

	public function registerMediaCollections(): void
	{ 
	    $this->addMediaCollection('attachments');
	}

    public static abstract function resourceScope(): string;
}
