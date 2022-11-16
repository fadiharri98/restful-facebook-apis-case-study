<?php
namespace Controllers;

use Constants\StatusCodes;

class LikeController extends BaseController
{
    protected function get(): array
    {
        list($post_id,) = func_get_args();

        // expect id, if there is no id, then validation exception response
        if (! $post_id)
        {
            return [
                'data' => 'id should passed within uri.',
                'status_code' => StatusCodes::VALIDATION_ERROR
            ];
        }
        elseif (! is_numeric($post_id))
        {
            return [
                'data' => 'id should be integer.',
                'status_code' => StatusCodes::VALIDATION_ERROR
            ];
        } else {
            $post_id = intval($post_id);
        }

        // posts data from database
        return [
            'data' => [
                [
                    'user_id' => rand(1000, 5000),
                    'post_id' => $post_id,
                    'created' => "25/11/2020 09:12:55",
                ],
                [
                    'user_id' => rand(1000, 5000),
                    'post_id' => $post_id,
                    'created' => "25/11/2020 09:12:55",
                ],
                [
                    'user_id' => rand(1000, 5000),
                    'post_id' => $post_id,
                    'created' => "25/11/2020 09:12:55",
                ],
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }

    protected function post(): array
    {
        // some actions
        return [
            'data' => [
                'message' => "[LikeController] Success"
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }
}