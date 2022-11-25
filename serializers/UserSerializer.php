<?php

namespace Serializers;


use Models\User;

class UserSerializer extends BaseSerializer
{
    /**
     * @return bool
     */
    public function validModel($model = null)
    {
        return ($model ?: $this->model) instanceof User;
    }

    /**
     * @return string[]
     */
    public function getSerializationFields()
    {
        return [
            'id',
            'name',
            'username',
            'email',
            'profile_img',
            'created'
        ];
    }

    public function toArray()
    {
        return [
            'profile_img' => $this->model->profile_img ?: 'https://default.jpg'
         ];
    }

}