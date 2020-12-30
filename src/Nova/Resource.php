<?php

namespace Zareismail\Hafiz\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Coroowicaksono\ChartJsIntegration\LineChart;
use Zareismail\NovaContracts\Nova\Resource as BaseResource;
use Zareismail\Shaghool\Nova\MeasurableResource;
use Zareismail\Shaghool\Models\ShaghoolReport;
use Zareismail\NovaPolicy\Helper;

abstract class Resource extends BaseResource
{ 
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Places'; 

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['details'];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
    ];

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return parent::indexQuery($request, $query);
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Scout\Builder  $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {  
        return static::authenticateQuery($request, parent::relatableQuery($request, $query));
    } 

    /**
     * Get the actions available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            Actions\SendNotification::make()
                ->showOnTableRow()
                ->canSee(function($request) {
                    return $request->user()->can('sendNotificatino', $this->resource);
                }), 
        ];
    }

    /**
     * Get the cards available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {  
        return static::authenticateQuery($request, $this->reportQuery($request))
                    ->with('percapita.resource')
                    ->whereDate('target_date', '>=', now()->subMonths(12))
                    ->get()
                    ->groupBy('percapita_id')
                    ->map(function($reports) {
                        return $this->newLineChart($reports);
                    })
                    ->values()
                    ->all();  
    }

    /**
     * Make a LinceChart for the given reports.
     * 
     * @param  \Illuminate\Database\Eloquent\Collection $reports 
     * @return \Coroowicaksono\ChartJsIntegration\LineChart          
     */
    public function newLineChart($reports)
    { 
        $series = $reports->groupBy(function($report) {
            return $report->target_date->format('M y');
        }); 

        $measurable = new MeasurableResource(
            $reports->pluck('percapita.resource')->filter()->first()
        );

        return (new LineChart) 
            ->title($measurable->title()) 
            ->animations([
                'enabled' => true,
                'easing' => 'easeinout',
            ])
            ->series(array([
                'barPercentage' => 0.5,
                'label' => __('Consumption'),
                'borderColor' => '#e53e3e',
                'data' => $consumption = $series->map->sum('value')->values()->all(),
            ],[
                'barPercentage' => 0.5,
                'label' => __('Balance'),
                'borderColor' => '#38a169',
                'data' => $balance = $series->map->sum('balance')->values()->all(),
            ],[
                'barPercentage' => 0.5,
                'label' => __('Consumption Aggregation'),
                'borderColor' => '#fed7d7',
                'data' => collect($consumption)->map(function($value, $key) use ($consumption) {
                    return collect($consumption)->slice(0, $key)->reduce(function($carry, $item) {
                        return $item + $carry;
                    }, $value);
                })->values()->all(),
            ],[
                'barPercentage' => 0.5,
                'label' => __('Balance Aggregation'),
                'borderColor' => '#c6f6d5',
                'data' => collect($balance)->map(function($value, $key) use ($balance) {
                    return collect($balance)->slice(0, $key)->reduce(function($carry, $item) {
                        return $item + $carry;
                    }, $value);
                })->values()->all(),
            ]))
            ->options([
                'xaxis' => [
                    'categories' => $series->keys()->all(),
                ],
            ])
            ->width('1/2')
            ->withMeta([ 
                'onlyOnDetail' => request()->filled('resourceId'),
            ]); 
    }

    /**
     * Build report query for the given request.
     * 
     * @param  \Illuminate\Http\Request $request 
     * @return \Illuminate\Database\Eloquent\Builder           
     */
    public function reportQuery(Request $request)
    {
        return ShaghoolReport::whereHas('percapita', function($query)  {
            $query->whereHasMorph('measurable', [static::$model], function($query) {
               $query->when(request()->filled('resourceId'), function($query) {
                    $query->whereKey(request()->input('resourceId'));
               });
            });
        });
    }
}