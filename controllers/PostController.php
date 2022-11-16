<?php
namespace Controllers;

use Constants\StatusCodes;

class PostController extends BaseController
{
    protected function get(...$params): array
    {
        list($user_id,) = $params;

        // expect id, if there is no id, then validation exception response
        if (! $user_id)
        {
            return [
                'data' => 'id should passed within uri.',
                'status_code' => StatusCodes::VALIDATION_ERROR
            ];
        }
        elseif (! is_numeric($user_id))
        {
            return [
                'data' => 'id should be integer.',
                'status_code' => StatusCodes::VALIDATION_ERROR
            ];
        } else {
            $user_id = intval($user_id);
        }

        // posts data from database
        return [
            'data' => [
                [
                    'title' => "Foo",
                    'description' => "foo description",
                    'created' => "25/11/2020 09:12:55",
                    'user_id' => $user_id
                ],
                [
                    'title' => "Foo foo",
                    'description' => "foo x2 description",
                    'created' => "25/11/2020 09:19:25",
                    'user_id' => $user_id
                ],
                [
                    'title' => "Bar",
                    'description' => "bar description",
                    'created' => "25/11/2020 09:14:15",
                    'user_id' => $user_id
                ]
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }

    protected function post(...$params): array
    {
        list($user_id, ) = $params;

        // some actions
        return [
            'data' => [
                'message' => "[PostController.POST] User #$user_id create new post."
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }

    protected function put(...$params): array
    {
        list($user_id, $post_id) = $params;

        // some actions
        return [
            'data' => [
                'message' => "[PostController.PUT] Post #$post_id successfully updated by User #$user_id."
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }

    protected function delete(...$params): array
    {
        list($user_id, $post_id) = $params;

        // some actions
        return [
            'data' => [
                'message' => "[PostController.DELETE] Post #$post_id successfully deleted by User #$user_id."
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }
}