<?php

namespace Controllers;

use Constants\Rules;
use Constants\StatusCodes;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Models\Foo;
use Serializers\FooSerializer;

class FooController extends BaseController
{
    protected array $validationSchema = [
        'show' => [
            'url' => [
                'foo_id' => [Rules::INTEGER]
            ]
        ],
        'create' => [
            'payload' => [
                'dummy' => [Rules::REQUIRED, Rules::STRING, Rules::NOT_EMPTY]
            ]
        ],
        'update' => [
            'payload' => [
                'dummy' => [Rules::REQUIRED, Rules::STRING, Rules::NOT_EMPTY]
            ]
        ]
    ];

    public function __construct()
    {
        parent::__construct();
    }

    // GET api/v1/foo/{foo_id}
    protected function show($foo_id)
    {
        $foo =
            ResourceHelper::findResource(
                Foo::class,
                $foo_id,
                ['bars']
            );

        $serializer = new FooSerializer($foo);

        return [
            'data' => $serializer->serialize(),
        ];
    }

    # POST api/v1/foo
    protected function create()
    {
        $payload = RequestHelper::getRequestPayload();

        $foo = Foo::create($payload);

        return [
            'data' => [
                'foo_id' => $foo->id
            ],
            'status_code' => StatusCodes::CREATED
        ];
    }

    // PUT api/v1/foo/{foo_id}
    protected function update($foo_id)
    {
        $payload = RequestHelper::getRequestPayload();

        /**
         * @var Foo $foo
         */
        $foo = ResourceHelper::findResource(Foo::class, $foo_id);

        $foo->update([
            'dummy' => $payload['dummy']
        ]);

        return [
            'data' => [
                'message' => "Foo #$foo_id has been successfully updated."
            ]
        ];
    }

    // DELETE api/v1/foo/{foo_id}
    protected function destroy($foo_id)
    {
        /**
         * @var Foo $foo
         */
        $foo = ResourceHelper::findResource(Foo::class, $foo_id);

        $foo->delete();

        return [
            'data' => [
                'message' => "Foo #$foo_id has been successfully deleted."
            ]
        ];
    }

}