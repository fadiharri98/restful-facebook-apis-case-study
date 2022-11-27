<?php

namespace Controllers;

use Constants\Rules;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Models\Post;
use Serializers\PostSerializer;

class PostController extends BaseController
{
    protected array $validationSchema = [
        'update' => [
            'url' => [
                'post_id' => [Rules::INTEGER]
            ],
            'payload' => [
                'content' => [Rules::REQUIRED, Rules::STRING, Rules::NOT_EMPTY]
            ]
        ],
        'destroy' => [
            'url' => [
                'post_id' => [Rules::INTEGER]
            ]
        ]
    ];

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

    protected function update($post_id)
    {
        $payload = RequestHelper::getRequestPayload();

        ResourceHelper::findResource(Post::class, $post_id)
            ->update([
                'content' => $payload['content']
            ]);

        return [
            'data' => [
                'message' => "Post #$post_id has been successfully updated."
            ]
        ];
    }

    protected function destroy($post_id)
    {
        ResourceHelper::findResource(Post::class, $post_id)
            ->delete();

        return [
            'data' => [
                'message' => "Post #$post_id has been successfully deleted."
            ]
        ];
    }
}