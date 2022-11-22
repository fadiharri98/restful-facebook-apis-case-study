<?php

namespace Controllers;

use Constants\Rules;
use Constants\StatusCodes;
use CustomExceptions\ResourceNotFoundException;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Models\User;

class UserController extends BaseController
{
    protected array $validationSchema = [
        'show' => [
            'url' => [
                'user_id' => [Rules::INTEGER,]
            ]
        ],
        'index' => [
            'query' => [
                'limit' => [Rules::INTEGER],
                'page' => [Rules::INTEGER],
            ]
        ],
        "create" => [
            'payload' => [
                'name' => [Rules::REQUIRED, Rules::STRING, Rules::NOT_EMPTY],
                'email' => [
                    Rules::REQUIRED,
                    Rules::EMAIL,
                    Rules::UNIQUE => [
                        'resource' => User::class,
                    ],
                ],
                'username' => [
                    Rules::REQUIRED,
                    Rules::STRING,
                    Rules::NOT_EMPTY,
                    Rules::UNIQUE => [
                        'resource' => User::class,
                    ],
                ],
                'password' => [Rules::REQUIRED, Rules::STRING],
                'profile_img' => [Rules::STRING],
            ]
        ],
        "update" => [
            'url' => [
                'user_id' => [Rules::INTEGER],
            ],
            'payload' => [
                'name' => [Rules::STRING, Rules::NOT_EMPTY],
                'username' => [
                    Rules::STRING,
                    Rules::NOT_EMPTY,
                    Rules::UNIQUE => [
                        'resource' => User::class,
                    ],
                ],
                'email' => [Rules::EMAIL],
                'profile_img' => [Rules::STRING],
            ]
        ],
        'destroy' => [
            'url' => [
                'user_id' => [Rules::INTEGER]
            ]
        ],
    ];

    /**
     * @param integer $user_id
     * @return array
     * @throws ResourceNotFoundException if no match user by id.
     */
    protected function show(int $user_id): array
    {
        $user = ResourceHelper::findResource(User::class, $user_id);

        return [
            'data' => $user,
            'status_code' => StatusCodes::SUCCESS
        ];
    }

    protected function index()
    {
        $perPage = $_GET['limit'] ?? 15;
        $currentPage = $_GET['page'] ?? 1;

        $paginateUsers = User::query()->paginate($perPage, ['*'], 'page', $currentPage);

        return [
            'data' => $paginateUsers->items(),
            'pagination' => [
                "current_page" => $paginateUsers->currentPage(),
                "per_page" => $paginateUsers->perPage(),
                "last_page" => $paginateUsers->lastPage(),
                "total" => $paginateUsers->total()
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }

    protected function create()
    {
        $payload = RequestHelper::getRequestPayload();
        $payload['password'] = md5($payload['password']);

        $created_user = User::create($payload);

        return [
            'data' => [
                'id' => $created_user->id
            ],
            'status_code' => StatusCodes::CREATED
        ];
    }

    protected function update($user_id)
    {
        /**
         * @var User $user
         */
        $user = ResourceHelper::findResource(User::class, $user_id);
        /**
         * array $payload
         */
        $payload = RequestHelper::getRequestPayload();

        $user->update($payload);

        return [
            'data' => [
                'message' => "User #$user_id has been successfully updated"
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }

    protected function destroy($user_id)
    {
        /**
         * @var User $user
         */
        $user = ResourceHelper::findResource(User::class, $user_id);

        $user->delete();

        return [
            'data' => [
                'message' => "User #$user_id has been successfully deleted"
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }
}