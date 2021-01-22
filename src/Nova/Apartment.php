<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Http\Request; 
use Laravel\Nova\Http\Requests\NovaRequest; 
use Laravel\Nova\Fields\{ID, Heading, Number, Trix, BelongsTo, HasMany, MorphMany};
use Superlatif\NovaTagInput\Tags;
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
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
        'id', 'number'
    ];

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['costs', 'details', 'building', 'contracts', 'percapitas.resource.unit'];

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

            Number::make(__('Code'), function() {
                return "<b>{$this->code}</b>";
            })->asHtml()->exceptOnForms(),

            BelongsTo::make(__('User'), 'auth', User::class)
                ->withoutTrashed()
                ->default($request->user()->getKey())
                ->searchable()
                ->canSee(function($request) {
                    return $request->user()->can('addUser', static::newModel());
                }),  

            CascadeBelongsTo::make(__('Building'), 'building', Building::class)
                ->readonly($request->viaResource() === Complex::class)
                ->searchable(),

            BelongsTo::make(__('Complex'), 'complex', Complex::class)
                ->exceptOnForms()
                ->showCreateRelationButton(),

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

            Tags::make(__('Inventory List'), 'config->inventory')
                ->help(__('Write inventory name and press the ENTER ...'))
                ->placeholder(__('Write inventory name and press the ENTER ...'))
                ->allowEditTags()
                ->hideFromIndex(),

    		Trix::make(__('Description'), 'description') 
    			->help(__('Write about your apartment and their features.'))
    			->withFiles('public'), 

            $this->when($request->isResourceDetailRequest() && $this->percapitas->isNotEmpty(), function() {
                return new Fields\PerCapitas($this->percapitas);
            }), 

            new Fields\Details($this),  

            Medialibrary::make(__('Gallery'), 'gallery')
                ->attachExisting()
                ->autouploading()
                ->attachExisting(function ($query, $request, $model) {
                    $query->authenticate();
                }), 
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
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if(Helper::isTenant($request->user())) {
            return $query->tap(function($query) {
                $query->whereHas('contracts', function($query) {
                    $query->authenticate();
                });
            });
        }

        return parent::indexQuery($request, $query); 
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
}