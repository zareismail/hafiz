<?php

namespace Zareismail\Hafiz\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Zareismail\Hafiz\Nova\Complex as ComplexResource;

class Complex extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->whereHas('complex', function($query) use ($value) {
            $query->whereKey($value);
        });
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return ComplexResource::newModel()->when($request->user()->cant('delete', ComplexResource::newModel()), function($query) {
            $query->authenticate();
        })->get()->mapInto(ComplexResource::class)->keyBy->title()->map->getKey();
    }
}
