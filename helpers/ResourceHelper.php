<?php

namespace Helpers;

use CustomExceptions\ResourceNotFoundException;

class ResourceHelper
{
    /**
     * @throws ResourceNotFoundException if no match resource by id
     */
    public static function findResource($model, $resource_id, $resource_name=null)
    {
        $resource = $model::query()->find($resource_id);

        if(! $resource)
        {
            $_ = explode("\\", $model);
            throw new ResourceNotFoundException($resource_name ?: array_pop($_));
        }

        return $resource;
    }
}