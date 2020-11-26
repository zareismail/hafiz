<?php

namespace Zareismail\Hafiz\Nova; 

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text, Number, Select, DateTime, BelongsTo, MorphTo, HasMany};   
use Zareismail\NovaContracts\Nova\User;
use Armincms\Fields\Chain;   

class EnvironmentalReport extends Reports
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Hafiz\Models\HafizEnvironmentalReport::class;

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['environmental'];

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
                ->searchable()
                ->default($request->user()->id)
                ->canSee(function($request) {
                    return $request->user()->can('update', static::newModel());
                }),

            Chain::as('environmental', function() {
                return [ 
                    Select::make(__('Environmental'), 'environmental_id')
                        ->displayUsingLabels()
                        ->options(Environmental::newModel()->get()->pluck('name', 'id')),
                ];
            }),

            Chain::with('environmental', function($request) { 
                $resources = $this->resources($request)->map(function($resource) {
                    return [
                        'label' => $resource::label(),
                        'key' => $resource::newModel()->getMorphClass(),
                    ];
                });
                return [ 
                    Select::make(__('Environmental'), 'reportable_type') 
                        ->options($resources->pluck('label', 'key'))
                        ->rules('required')
                        ->required(),
                ];
            }, 'resources'),

            Chain::with('resources', function($request) { 
                $resource = $this->resources($request)->first(function($resource) {
                    return $resource::newModel()->getMorphClass() == request('reportable_type');
                });

                if(is_null($resource)) return [];

                $options = $resource::newModel()->get()->mapInto($resource)->keyBy('id')->map->title();

                return [ 
                    Select::make($resource::label(), 'reportable_id') 
                        ->options($options)
                        ->rules('required')
                        ->required(),
                ];
            }),

            BelongsTo::make(__('Environmental'), 'environmental', Environmental::class) 
                ->withoutTrashed()
                ->searchable()
                ->exceptOnForms(),

            MorphTo::make(__('Reportable'), 'reportable') 
                ->types($this->resources($request)->all())
                ->withoutTrashed()
                ->searchable()
                ->exceptOnForms(),

    		DateTime::make(__('Target Date'), 'target_date')
    			->required()
    			->rules('required')
    			->help(__('What date is the report for?')), 

            Number::make(__('Consumption'), 'value')
                ->required()
                ->rules('required', function($attribute, $value, $fail) use ($request) {
                    $environmental = $this->getEnvironmental($request);

                    $consumptions = collect($environmental->getConfig('options'))->map(function($option) {
                        return request()->get($option);
                    });

                    if($consumptions->isNotEmpty() && $consumptions->sum() != $value) {
                        // $fail(__('The total consumption is not equal to the sum of consumptions.'));
                    } 
                })
                ->help(__('What is the total consumption?')), 

            Chain::with('environmental', [$this, 'consumptions']),

            $this->mergeWhen($request->isResourceDetailRequest(), $this->getEnvironmentalFields($this->environmental)),
    	];
    }

    public function consumptions($request)
    {  
        return $this->getEnvironmentalFields($this->getEnvironmental($request)); 
    }

    public function getEnvironmentalFields($environmental)
    {
        return  collect(optional($environmental)->getConfig('options', []))
                ->flatMap(function($option) use ($environmental) {
                    return [
                        Number::make($option, Str::slug($option))
                            ->required()
                            ->rules('required')
                            ->fillUsing(function($request, $model, $attribute) use ($option) { 
                                $model->setAttribute(
                                    "details->{$option}", $request->get($attribute)
                                );
                            })
                            ->resolveUsing(function($value, $model, $attribute) use ($option) { 
                                return $model->getDtails($option);
                            })
                            ->help(__('Enter :environmental consumption amount per the :unit', [
                                'environmental' => $environmental->name,
                                'unit' => $environmental->unit->label,
                            ])),
                    ];
                })->all();
    }

    public function getEnvironmental(Request $request)
    {
        return Environmental::newModel()->with('unit')->find($request->get('environmental_id'));
    }

    public function resources(Request $request)
    {
        return Environmental::newModel()->sharedResources($request);
    }
}