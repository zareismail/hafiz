<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request; 
use Laravel\Nova\Http\Requests\NovaRequest; 
use Laravel\Nova\Fields\{ID, Heading, Number, Trix, BelongsTo, HasMany, MorphMany};
use Superlatif\NovaTagInput\Tags; 
use Zareismail\Fields\{BelongsTo as CascadeBelongsTo, MorphTo as CascadeMorphTo};
use Zareismail\NovaContracts\Nova\User;
use Zareismail\Costable\Nova\Cost;
use Zareismail\Hafiz\Helper;

class Apartment extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizApartment::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'number';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'number', 'code'
    ];

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['building', 'details', 'auth', 'media'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
    	return [
    		ID::make(), 

            BelongsTo::make(__('User'), 'auth', User::class)
                ->withoutTrashed()
                ->default($request->user()->getKey())
                ->searchable()
                ->hideFromIndex()
                ->canSee(function($request) {
                    return $request->user()->can('addUser', static::newModel());
                }),  

            CascadeBelongsTo::make(__('Building'), 'building', Building::class)
                ->readonly($request->viaResource() === Complex::class)
                ->searchable(),

            Number::make(__('Code'), function() {
                return "<b>{$this->code}</b>";
            })->asHtml()->exceptOnForms(),

            Number::make(__('Floor'), 'floor')
                ->required()
                ->rules('required')
                ->help(__('What is the floor number of your apartment?'))
                ->min(0)
                ->default(0), 

    		Number::make(__('Number'), 'number')
    			->required()
    			->rules('required', function($attribute, $value, $fail) { 
                    if(! $this->apartmentExists($value)) {
                        $fail(__('The apartment :number already exists.', ['number' => request('number')])); 
                    }
                })
    			->help(__('What is the number of your apartment?'))
                ->min(0)
                ->default(0),

    		Trix::make(__('Description'), 'description') 
    			->help(__('Write about your apartment and their features.'))
    			->withFiles('public'), 

            $this->when($request->isResourceDetailRequest() && $this->percapitas->isNotEmpty(), function() {
                return new Fields\PerCapitas($this->percapitas);
            }), 

            $this->when($request->route('resource') === static::uriKey(), function() {
                return new Fields\Details($this);
            }),  

            $this->mergeGalleryField(),
    	];
    }

    public function apartmentExists(int $value = null)
    {
        return static::newModel()->whereHas('building', function($query) {
                    $query->whereKey(request('building'));
                })
                ->whereFloor(request('floor'))
                ->whereNumber($value)
                ->whereKeyNot($this->id)
                ->count() == 0;
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    { 
        return forward_static_call([new Building($this->building), 'title']).': '.$this->number;
    } 

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizeToViewAny(Request $request)
    {
        return $this->inContract($request) || parent::authorizedToView($request);
    } 

    /**
     * Determine if the current user can view the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToView(Request $request)
    {
        return $this->inContract($request) || parent::authorizeToView($request);
    } 

    /**
     * Determine if the current user can view the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizedToView(Request $request)
    {
        return parent::authorizedToView($request) || $this->inContract($request);
    }

    /**
     * Determine if the current user can view the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function inContract(Request $request)
    {
        return collect($this->contracts)->filter(function($contract) use ($request) {
            return $contract->auth_id === $request->user()->id;
        })->isNotEmpty();
    } 

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applySearch($query, $search)
    {   
        $buildingSerachCallback = function($query) use ($search) {
            $search = trim(preg_replace('/[0-9]+$/', '', $search));

            Building::buildIndexQuery(app(NovaRequest::class), $query, $search); 
        };

        preg_match('/([0-9]+)$/', $search, $matches);


        return $query->when(
            ! isset($matches[0]), 
            function($query) use ($search, $buildingSerachCallback) {
                parent::applySearch($query, $search)->orWhereHas('building', $buildingSerachCallback);
            }, 
            function($query) use ($matches, $buildingSerachCallback) {
                parent::applySearch($query, $matches[0])->whereHas('building', $buildingSerachCallback);
            });
    }  
    
    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyPerCapitaSearch($query, $search)
    { 
        static::applyRelatedSearch($query, $search);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyCostSearch($query, $search)
    { 
        static::applyRelatedSearch($query, $search);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyRelatedSearch($query, $search)
    { 
        $query->orWhereHas('building', function($query) use ($search) {
            Building::applyRelatedSearch($query->where('name', 'like', '%'.$search.'%'), $search); 
        })->when(preg_match('/[0-9]+$/', $search), function($query) use ($search) {
            $query->orWhere(function($query) use ($search) {
                $parts = explode(' ', $search);

                $query
                    ->where('number', 'like', '%'.array_pop($parts).'%')
                    ->whereHas('building', function($query) use ($parts) {
                        $search = implode(' ', $parts);

                        Building::applyRelatedSearch($query->where('name', 'like', '%'.$search.'%'), $search); 
                    });
            });              
        });
    }

    /**
     * Get the cards available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {  
        return array_merge([
            Metrics\CreatedApartments::make(),
        ], parent::cards($request));  
    }

    /**
     * Get the filters available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            Filters\Complex::make(),

            Filters\Building::make(),
        ];
    }
}