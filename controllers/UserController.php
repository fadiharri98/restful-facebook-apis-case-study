<?php

namespace Controllers;

use Constants\Rules;
use Constants\StatusCodes;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Mixins\AuthenticateUser;
use Models\User;
use Serializers\UserSerializer;

class UserController extends BaseController
{
    use AuthenticateUser;

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
                'profile_img' => [Rules::STRING, Rules::NOT_EMPTY],
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

    // GET api/v1/users/{user_id}
    protected function show(int $user_id): array
    {
        /**
         * @var User $user
         */
        $user = ResourceHelper::findResource(User::class, $user_id);

        if ($user->id != $this->authenticatedUser->id) {
            ResourceHelper::validateUserIsAdmin($this->authenticatedUser);
        }

        $serializer = new UserSerializer($user);

        return [
            'data' => $serializer->serialize(),
        ];
    }

    // GET api/v1/users
    protected function index()
    {
        ResourceHelper::validateUserIsAdmin($this->authenticatedUser);

        $serializer =
            new UserSerializer(
                ResourceHelper::getResourcesPaginated(User::class)
            );

        return $serializer->paginatorSerialize();
    }

    // POST api/v1/users
    protected function create()
    {
        ResourceHelper::validateUserIsAdmin($this->authenticatedUser);

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

    // PUT api/v1/users
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

        if ($user->id != $this->authenticatedUser->id)
        {
            ResourceHelper::validateUserIsAdmin($this->authenticatedUser);
        }

        $user->update($payload);

        return [
            'data' => [
                'message' => "User #$user_id has been successfully updated"
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }

    // DELETE api/v1/users
    protected function destroy($user_id)
    {
        /**
         * @var User $user
         */
        $user = ResourceHelper::findResource(User::class, $user_id);

        ResourceHelper::validateUserIsAdmin($this->authenticatedUser);

        $user->delete();

        return [
            'data' => [
                'message' => "User #$user_id has been successfully deleted"
            ],
            'status_code' => StatusCodes::SUCCESS
        ];
    }

}