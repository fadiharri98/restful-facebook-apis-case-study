<?php

namespace Helpers;

use CustomExceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Builder;

class ResourceHelper
{
    /**
     * @throws ResourceNotFoundException if no match resource by id
     */
    public static function findResource($model, $resource_id, $with=[], $resource_name=null)
    {
        /**
         * @var Builder $query
         */
        $query = $model::query();

        if($with)
        {
            $query = $query->with($with);
        }

        $resource = $query->find($resource_id);

        if(! $resource)
        {
            $_ = explode("\\", $model);
            throw new ResourceNotFoundException($resource_name ?: array_pop($_));
        }

        return $resource;
    }
}