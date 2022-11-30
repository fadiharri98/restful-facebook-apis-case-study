<?php

namespace Serializers;

use Models\Comment;

class CommentSerializer extends BaseSerializer
{
    protected $mapFieldWithCustomName = [
        'content' => 'comment'
    ];

    /**
     * @return bool
     */
    public function validModel($model = null)
    {
        return ($model ?: $this->model) instanceof Comment;

    }

    /**
     * @return string[]
     */
    public function getSerializationFields()
    {
        return [
            'id',
            'content',
            'user:id,name,profile_img',
            'created'
        ];

    }

}
