<?php

/**
 * Created by PhpStorm.
 * User: eric
 * Date: 16/3/12
 * Author: eric <eric@winhu.com>.
 */

namespace Wind\Http\Controllers;

use Illuminate\Http\Request;
use Wind\Facades\ConfigRepository as ConfRepo;
use Wind\Transformers\ConfigTransformer;

/**
 * 配置管理
 * Date: 16/3/12
 * Author: eric <eric@winhu.com>.
 */
class ConfigController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->r = app()->make('config_repository');
        parent::__construct($request);
    }
    /**
     * 返回指定配置信息.
     *
     * @param int $id
     *
     * @return $this
     */
    public function show($id = 0)
    {
        $list = ConfRepo::find(intval($id));

        return $this->item($list, new ConfigTransformer(), 1, '获取成功');
    }

    /**
     * 返回配置信息.
     *
     * @return $this
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 15);
        $list = ConfRepo::searchData($request->all(), $per_page);

        return $this->paginate($list, new ConfigTransformer(), 1, '获取成功');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        ConfRepo::validate($data);
        $list = ConfRepo::create($data);
        ConfRepo::afterProcess(2, $data);

        return $this->item($list, new ConfigTransformer(), 1, '新增成功');
    }

    /**
     *更新配置信息.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return $this
     */
    public function update(Request $request, $id = 0)
    {
        $data = $request->all();
        ConfRepo::updateValidator($data, $id);
        $model = ConfRepo::find($id);
        $data['old_name'] = $model->name;

        $list = ConfRepo::update(intval($id), $data);
        ConfRepo::afterProcess(1, $data);

        return $this->item($list, new ConfigTransformer(), 1, '更新成功');
    }

    /**
     * 删除配置信息.
     *
     * @param int $id
     *
     * @return $this
     */
    public function delete($id = 0)
    {
        // $model = ConfRepo::find($id);
        // $data['name'] = $model->name;

        $list = ConfRepo::delete($id);
        // ConfRepo::afterProcess(3, $data);

        return $this->item($list, new ConfigTransformer(), 1, '操作成功');
    }
}
