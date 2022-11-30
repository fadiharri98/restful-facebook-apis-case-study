<?php

namespace Controllers;

use Constants\Rules;
use CustomExceptions\ValidationException;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Illuminate\Database\QueryException;
use Mixins\AuthenticateUser;
use Models\Like;
use Models\Post;
use Models\User;
use Serializers\PostSerializer;

class PostController extends BaseController
{
    use AuthenticateUser;

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
        ],
        'likesPost' => [
            'url' => [
                'post_id' => [Rules::INTEGER]
            ]
        ],
        'unlikesPost' => [
            'url' => [
                'post_id' => [Rules::INTEGER]
            ]
        ]
    ];

    public function __construct()
    {
        parent::__construct();

        $this->handlerSkipAuthentication = [
            'show'
        ];
    }

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

        /**
         * @var Post $post
         */
        ($post = ResourceHelper::findResource(Post::class, $post_id))
            ->update([
                'content' => $payload['content']
            ]);

        ResourceHelper::validateIfUserAllowedTo(
            $this->authenticatedUser,
            $post
        );

        return [
            'data' => [
                'message' => "Post #$post_id has been successfully updated."
            ]
        ];
    }

    protected function destroy($post_id)
    {
        /**
         * @var Post $post
         */
        ($post = ResourceHelper::findResource(Post::class, $post_id))
            ->delete();

        ResourceHelper::validateIfUserAllowedTo(
            $this->authenticatedUser,
            $post
        );

        return [
            'data' => [
                'message' => "Post #$post_id has been successfully deleted."
            ]
        ];
    }

    protected function likesPost($post_id)
    {
        $user = $this->authenticatedUser;
        /**
         * @var Post $post
         */
        $post = ResourceHelper::findResource(Post::class, $post_id);

        try {

            $post->likes()->create([
                'user_id' => $user->id
            ]);
        } catch (QueryException $e) {

            throw new ValidationException("You are already liked the post.");
        }

        return [
          'data' => [
              'message' => 'Success'
          ]
        ];
    }

    protected function unlikesPost($post_id)
    {
        $user = $this->authenticatedUser;
        /**
         * @var Post $post
         */
        $post = ResourceHelper::findResource(Post::class, $post_id);
        /**
         * @var Like $like
         */
        $like =
            Like::query()->where('user_id', $user->id)->where('post_id', $post->id)->first();

        if (! $like) {
            throw new ValidationException("You are already not liked the post.");
        }

        $like->delete();

        return [
            'data' => [
                'message' => 'Success'
            ]
        ];
    }
}