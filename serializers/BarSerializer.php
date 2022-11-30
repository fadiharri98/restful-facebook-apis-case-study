<?php

namespace Serializers;


use Models\Bar;

class BarSerializer extends BaseSerializer
{
    /**
     * @return bool
     */
    public function validModel($model = null)
    {
        return ($model ?: $this->model) instanceof Bar;
    }

    /**
     * @return string[]
     */
    public function getSerializationFields()
    {
        return [
            'id',
            'value',
            'created'
        ];
    }

    public function toArray()
    {
        return [
            'id' => "bar-model-" . $this->model->id
        ];
    }

}