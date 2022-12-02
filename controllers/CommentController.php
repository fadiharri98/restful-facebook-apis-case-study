<?php

namespace Controllers;

use Constants\Rules;
use Helpers\ResourceHelper;
use Mixins\AuthenticateUser;
use Models\Comment;

class CommentController extends BaseController
{
    use AuthenticateUser;

    protected array $validationSchema = [
        'update' => [
            'url' => [
                'user_id' => [Rules::INTEGER]
            ],
            'payload' => [
                'content' => [Rules::REQUIRED, Rules::STRING, Rules::NOT_EMPTY]
            ]
        ],
        'destroy' => [
            'url' => [
                'comment_id' => [Rules::INTEGER]
            ]
        ]
    ];

    // PUT api/v1/comments/{comment_id}
    public function update($comment_id)
    {
        /**
         * @var Comment $comment
         */
        $comment = ResourceHelper::findResource(Comment::class, $comment_id);

        $this->authenticatedUser->validateIsAuthorizedTo($comment);

        $comment->update([
            'content' => $this->payload['content']
        ]);

        return [
            'data' => [
                'message' => "the comment has been update."
            ]
        ];
    }

    // DELETE api/v1/comments/{comment_id}
    public function destroy($comment_id)
    {
        /**
         * @var Comment $comment
         */
        $comment = ResourceHelper::findResource(Comment::class, $comment_id);

        $this->authenticatedUser->validateIsAuthorizedTo($comment);

        $comment->delete();

        return [
          'data' => [
              'message' => "the comment has been deleted."
          ]
        ];
    }
}