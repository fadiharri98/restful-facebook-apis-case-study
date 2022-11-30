<?php

namespace Nested\Controllers;

use Constants\Rules;
use Constants\StatusCodes;
use Controllers\BaseController;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Models\Post;
use Models\User;
use Serializers\PostSerializer;

class UserPostController extends BaseController
{
    protected array $validationSchema = [
        'index' => [
            'url' => [
                'user_id' => [Rules::INTEGER],
            ],
            'query' => [
                'limit' => [Rules::INTEGER],
                'page' => [Rules::INTEGER],
            ]
        ],
        'create' => [
            'url' => [
                'user_id' => [Rules::INTEGER]
            ],
            'payload' => [
                'content' => [Rules::REQUIRED, Rules::STRING, Rules::NOT_EMPTY]
            ]
        ]
    ];

    // GET api/v1/users/{user_id}/posts
    protected function index($user_id)
    {
        $user =
            ResourceHelper::findResource(User::class, $user_id);

        $filters = [
            'user_id' => [
                'value' => $user->id
            ]
        ];

        $user_posts =
            ResourceHelper::getResourcesPaginated(
                Post::class,
                $filters,
                ['publisher_user', 'comments', 'comments.user', 'likes']
            );

        $serializer = new PostSerializer($user_posts);

        return $serializer->paginatorSerialize();
    }

    protected function create($user_id)
    {
        $payload = RequestHelper::getRequestPayload();

        $post =
            ResourceHelper::findResource(User::class, $user_id)->
            posts()->
            create([
                'content' => $payload['content']
            ]);

        return [
            'data' => [
                'post_id' => $post->id
            ],
            'status_code' => StatusCodes::CREATED
        ];
    }

}