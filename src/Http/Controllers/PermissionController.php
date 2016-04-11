<?php

namespace Wind\Http\Controllers;

use Illuminate\Http\Request;
use Wind\Facades\PermissionRepository as PermissionRepo;
use Wind\Transformers\PermissionTransformer;

/**
 * 权限控制器.
 *
 * @author scl <scl@winhu.com>
 */
class PermissionController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->r = app()->make('permission_repository');
        parent::__construct($request);
    }

    public function index(Request $request)
    {
        $data = PermissionRepo::getModel()->with('grant_apis')->orderBy('sort', 'asc')->get();
        $permissions = list_to_tree($data->toArray(), 'id', 'pid', 'children');

        return response_json(1, $permissions, 'menus');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        PermissionRepo::validate($data);

        $permission = PermissionRepo::create($data);

        return $this->item($permission, new PermissionTransformer(), 1, '创建成功');
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $permission = PermissionRepo::update($id, $data);

        return $this->item($permission, new PermissionTransformer(), 1, '修改成功');
    }

    public function show(Request $request, $id)
    {
        $permission = PermissionRepo::find($id);

        return $this->item($permission, new PermissionTransformer(), 1, '获取成功');
    }

    public function delete(Request $request, $id)
    {
        $permission = PermissionRepo::delete($id);

        return $this->item($permission, new PermissionTransformer(), 1, '删除成功');
    }

    /**
     * 权限增加授权API.
     *
     * @param Request $request [description]
     * @param [type]  $id      [description]
     */
    public function addGrantApis(Request $request, $id)
    {
        $permission = PermissionRepo::find($id);

        $api_str = $request->input('ids');
        $api_ids = parse_ids_from_str($api_str);
        PermissionRepo::addGrantApis($id, $api_ids);

        return $this->item($permission, new PermissionTransformer(), 1, '添加成功');
    }

    /**
     * 权限拖拽排序功能
     * 请求为json格式，如下
     * {
     *     "data": [
     *         {"id":8, "sort":2},
     *         {"id":9, "sort":4}
     *        ]
     *   }.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function updatePermissionSort(Request $request)
    {
        $data = $request->json('data');
        foreach ($data as $key => $value) {
            PermissionRepo::update($value['id'], ['sort' => $value['sort']]);
        }

        return response_json(1, $data, '顺序修改成功');
    }
}
