<?php

namespace Nested\Controllers;

use Constants\Rules;
use Controllers\BaseController;
use Helpers\ResourceHelper;
use Models\Like;
use Serializers\LikeSerializer;

class PostLikeController extends BaseController
{
    protected array $validationSchema = [
        'url' => [
            'post_id' => [Rules::INTEGER]
        ],
        'query' => [
            'limit' => [Rules::INTEGER],
            'page' => [Rules::INTEGER]
        ]
    ];

    protected function index($post_id)
    {
        $likes =
            ResourceHelper::getResourcesPaginated(
              Like::class,
              [
                  'post_id' => [
                      'value' => $post_id
                  ]
              ],
              ['user:id,name,profile_img']
            );

        $serializer = new LikeSerializer($likes);

        return $serializer->paginatorSerialize();
    }

}