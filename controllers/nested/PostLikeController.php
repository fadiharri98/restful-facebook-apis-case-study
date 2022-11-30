<?php

namespace Nested\Controllers;

use Constants\Rules;
use Controllers\BaseController;
use Helpers\ResourceHelper;
use Mixins\AuthenticateUser;
use Models\Like;
use Serializers\LikeSerializer;

class PostLikeController extends BaseController
{
    use AuthenticateUser;

    protected array $validationSchema = [
        'url' => [
            'post_id' => [Rules::INTEGER]
        ],
        'query' => [
            'limit' => [Rules::INTEGER],
            'page' => [Rules::INTEGER]
        ]
    ];

    // GET api/v1/posts/{post_id}/comments
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