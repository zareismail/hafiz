<?php

namespace Zareismail\Hafiz\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Zareismail\Hafiz\Models\HafizBuilding;

class CreatedBuildings extends CreatedResources
{  
    /**
     * Get initiated query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function newQuery(NovaRequest $request)
    {
        return HafizBuilding::authenticate();
    }
}
