<?php

namespace Controllers;

use Constants\Rules;
use Constants\StatusCodes;
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
        'create' => [
            'url' => [
                'user_id' => [Rules::INTEGER]
            ],
            'payload' => [
                'content' => [Rules::REQUIRED, Rules::STRING, Rules::NOT_EMPTY]
            ]
        ],
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

    // GET api/v1/posts/{post_id}
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

    # POST api/v1/posts
    protected function create()
    {
        $payload = RequestHelper::getRequestPayload();

        $user_id = $this->authenticatedUser->id;

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

    // PUT api/v1/posts/{post_id}
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

    // DELETE api/v1/posts/{post_id}
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

    // POST api/v1/posts/{post_id}/like
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

    // POST api/v1/posts/{post_id}/unlike
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