<?php
/**
 * Created by PhpStorm.
 * Date: 16/3/12
 * Author: eric <eric@winhu.com>.
 */

namespace Wind\Repositories;

class ConfigRepository extends AbstractRepository
{
    /**
     * 验证更新时表单数据.
     *
     * @param $data
     * @param $id
     *
     * @return \Illuminate\Validation\Validator
     */
    public function updateValidator($data, $id)
    {
        $name = 'required|unique:config,name,'.$id;

        $updateRules = [
            'name' => $name,
            'title' => 'required',
            'sort' => 'numeric',
        ];

        return $this->validate($data, $updateRules, true);
    }

    /**
     * 设置查询条件初始化.
     *
     * @param array $data
     * @param $per_page
     *
     * @return array
     */
    public function searchData(array $data, $per_page)
    {
        $model = $this->model;
        $list = array();
        $order_type = 'id';
        if (isset($data['order_type'])) {
            $order_type = trim($data['order_type']);
        }

        $name = isset($data['name']) ? $data['name'] : '';
        if (isset($data['group'])) {
            $list = $model::where('group', '=', (int) ($data['group']))
                    ->where('name', 'like', '%'.$name.'%')
                    ->orderBy($order_type, 'asc')
                    ->paginate($per_page);
        } else {
            $list = $model::where('name', 'like', '%'.$name.'%')
                    ->orderBy($order_type, 'asc')
                    ->paginate($per_page);
        }

        return $list;
    }

    public function afterProcess($type, $input)
    {
        reload_config_cache($type, $input);
    }
}
