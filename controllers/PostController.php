<?php

namespace Controllers;

use Helpers\ResourceHelper;
use Models\Post;
use Serializers\PostSerializer;

class PostController extends BaseController
{
    protected function show($post_id)
    {
        $post =
            ResourceHelper::findResource(
                Post::class,
                $post_id,
                ['publisher_user:id,name,profile_img', 'likes', 'comments']
            );

        $serializer = new PostSerializer($post);

        return [
            'data' => $serializer->serialize(),
        ];
    }
}