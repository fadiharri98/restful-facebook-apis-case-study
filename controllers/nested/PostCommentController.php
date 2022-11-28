<?php

namespace Nested\Controllers;

use Constants\Rules;
use Constants\StatusCodes;
use Controllers\BaseController;
use Helpers\ResourceHelper;
use Models\Comment;
use Models\Post;
use Models\User;
use Serializers\CommentSerializer;

class PostCommentController extends BaseController
{
    protected array $validationSchema = [
        'create' => [
            'url' => [
                'post_id' => [Rules::INTEGER]
            ],
            'payload' => [
                'user_id' => [Rules::REQUIRED, Rules::INTEGER],
                'content' => [Rules::STRING, Rules::REQUIRED, Rules::NOT_EMPTY]
            ]
        ],
        'index' => [
            'url' => [
                'post_id' => [Rules::INTEGER]
            ],
            'query' => [
                'limit' => [Rules::INTEGER],
                'page' => [Rules::INTEGER]
            ]
        ]
    ];

    protected function index($post_id)
    {
        $comments =
            ResourceHelper::getResourcesPaginated(
                Comment::class,
                [
                    'post_id' => [
                        'value' => $post_id
                    ]
                ],
                ['user:id,name,profile_img']
            );

        $serializer = new CommentSerializer($comments);

        return $serializer->paginatorSerialize();
    }

    protected function create($post_id)
    {
        /**
         * @var User $user
         */
        $user = ResourceHelper::findResource(User::class, $this->payload['user_id']);

        /**
         * @var Comment $comment
         */
        $comment =
            ResourceHelper::findResource(Post::class, $post_id)->
            comments()->
            create([
                'user_id' => $user->id,
                'content' => $this->payload['content']
            ]);

        return [
            'data' => [
                'comment_id' => $comment->id
            ],
            'status_code' => StatusCodes::CREATED
        ];
    }
}