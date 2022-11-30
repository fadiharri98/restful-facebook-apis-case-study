<?php

namespace Serializers;


use Models\Foo;

class FooSerializer extends BaseSerializer
{
    /**
     * @return bool
     */
    public function validModel($model = null)
    {
        return ($model ?: $this->model) instanceof Foo;
    }

    /**
     * @return string[]
     */
    public function getSerializationFields()
    {
        return [
            'id',
            'dummy',
            'bars:id,value,created',
            'created'
        ];
    }

    protected function toArray()
    {
        return [
            'id' => "foo-model-" . $this->model->id
        ];
    }
}