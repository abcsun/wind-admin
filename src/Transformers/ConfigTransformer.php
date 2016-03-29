<?php

namespace Wind\Transformers;

use League\Fractal\TransformerAbstract;
use Wind\Models\ConfigModel;
/**
 *
 * Date: 16/3/26
 * Author: eric <eric@winhu.com>
 */
class ConfigTransformer extends TransformerAbstract
{
    /**
     * 将config数据转换为固定格式.
     *
     * @return array
     */
    public function transform(ConfigModel $model)
    {

        return [
            'id' => (int)$model->id,
            'type' => $model->type,
            'group' => $model->group,
            'sort' => $model->sort,
            'name'  => $model->name,
            'title' => $model->title,
            'value' => $model->value,
            'remark' => $model->remark,
            'x_status' => $model->x_status,
//            'updated_at' => $model->updated_at,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
            'deleted_at' => $model->deleted_at,
        ];
    }
}