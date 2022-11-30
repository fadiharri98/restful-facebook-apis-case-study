<?php

namespace Nested\Controllers;

use Constants\Rules;
use Constants\StatusCodes;
use Controllers\BaseController;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Models\Bar;
use Models\Foo;
use Serializers\BarSerializer;

class FooBarController extends BaseController
{
    protected array $validationSchema = [
        'show' => [
            'url' => [
                'foo_id' => [Rules::INTEGER],
            ]
        ],
        'create' => [
            'url' => [
                'foo_id' => [Rules::INTEGER],
            ],
            'payload' => [
                'value' => [Rules::REQUIRED, Rules::INTEGER]
            ]
        ]
    ];

    // GET api/v1/foo/{foo_id}/bar/{bar_id}
    protected function show($foo_id, $bar_id)
    {
        $bar =
            ResourceHelper::findResource(Bar::class, $bar_id);

        $serializer = new BarSerializer($bar);

        return [
            'data' => $serializer->serialize()
        ];
    }

    // POST api/v1/foo/{foo_id}/bar
    protected function create($foo_id)
    {
        $payload = RequestHelper::getRequestPayload();
        /**
         * @var Foo $foo
         */
        $foo =
            ResourceHelper::findResource(Foo::class, $foo_id);
        /**
         * @Var Bar $bar
         */
        $bar =
            $foo->bars()->create([
                'value' => $payload['value']
            ]);

        return [
            'data' => [
                'id' => $bar->id
            ],
            'status_code' => StatusCodes::CREATED
        ];
    }
}