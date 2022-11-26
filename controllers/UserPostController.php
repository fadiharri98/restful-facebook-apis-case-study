<?php

namespace Controllers;

use Constants\Rules;
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
        ]
    ];

    protected function index($user_id)
    {
        $user = ResourceHelper::findResource(User::class, $user_id);

        $filters = [
            'user_id' => [
                'value' => $user->id
            ]
        ];

        $user_posts =
            ResourceHelper::getResourcesPaginated(Post::class, $filters);

        $serializer = new PostSerializer($user_posts);

        return $serializer->paginatorSerialize();
    }

}