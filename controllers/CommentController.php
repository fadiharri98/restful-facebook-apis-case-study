<?php

namespace Controllers;

use Constants\Rules;
use Helpers\ResourceHelper;
use Models\Comment;

class CommentController extends BaseController
{
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

    public function update($comment_id)
    {
        ResourceHelper::findResource(Comment::class, $comment_id)->
        update([
            'content' => $this->payload['content']
        ]);

        return [
            'data' => [
                'message' => "the comment has been update."
            ]
        ];
    }

    public function destroy($comment_id)
    {
        ResourceHelper::findResource(Comment::class, $comment_id)->delete();

        return [
          'data' => [
              'message' => "the comment has been deleted."
          ]
        ];
    }
}