<?php

namespace Serializers;


use Models\Post;

class PostSerializer extends BaseSerializer
{
    protected $mapRelationSerializer = [
        "publisher_user" => UserSerializer::class
    ];

    /**
     * @return bool
     */
    public function validModel($model = null)
    {
        return ($model ?: $this->model) instanceof Post;
    }

    /**
     * @return string[]
     */
    public function getSerializationFields()
    {
        return [
            'id',
            'content',
            'publisher_user:id,name,profile_img',
            'likes_count',
            'recent_user_likes',
            'comments_count',
            'recent_comments',
            'created'
        ];
    }

}