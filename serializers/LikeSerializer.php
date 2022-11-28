<?php

namespace Serializers;

use Models\Like;

class LikeSerializer extends BaseSerializer
{
    /**
     * @return bool
     */
    public function validModel($model = null)
    {
        return ($model ?: $this->model) instanceof Like;

    }

    /**
     * @return string[]
     */
    public function getSerializationFields()
    {
        return [
            'id',
            'user:id,name,profile_img',
            'created'
        ];

    }

}
