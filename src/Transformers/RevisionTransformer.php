<?php
/**
 * Date: 16/3/23
 * Author: eric <eric@winhu.com>.
 */

namespace Wind\Transformers;

use League\Fractal\TransformerAbstract;
use Wind\Models\RevisionModel;

class RevisionTransformer extends TransformerAbstract
{
    /**
     * 将RevisionModel数据转换为固定格式.
     *
     * @return array
     */
    public function transform(RevisionModel $model)
    {
        $key = $model->key;
        $type = $model->revisionable_type;
        $id = $model->revisionable_id;
        $value = $model->new_value;

        if ($model->key == 'password') {
            $value = substr($value, 0, 5).'……';
        }

        return [
            'id' => (int) $model->id,
            'updated_at' => $model->updated_at,
            'description' => return_revision_description($key, $type, $id, $value),
            'user_name' => $model->name,
        ];
    }
}
