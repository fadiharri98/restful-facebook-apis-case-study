<?php
require_once __DIR__ . "/BaseController.php";

class PostController extends BaseController
{
    public static function get()
    {
        // posts data from database
        return [
            'data' => [
                [
                    'title' => "Foo",
                    'description' => "foo description",
                    'created' => "25/11/2020 09:12:55",
                    'user_id' => 1
                ],
                [
                    'title' => "Foo foo",
                    'description' => "foo x2 description",
                    'created' => "25/11/2020 09:19:25",
                    'user_id' => 1
                ],
                [
                    'title' => "Bar",
                    'description' => "bar description",
                    'created' => "25/11/2020 09:14:15",
                    'user_id' => 2
                ]
            ],
            'status_code' => 200
        ];
    }

    public static function post()
    {
        // some actions
        return [
            'data' => [
                'message' => "Success"
            ],
            'status_code' => 200
        ];
    }
}