<?php

namespace Wind\Http\Controllers;

use Illuminate\Http\Request;
use Wind\Facades\RoleRepository as RoleRepo;
use Wind\Transformers\RoleTransformer;

/**
 * 角色控制器.
 *
 * @author scl <scl@winhu.com>
 */
class RoleController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->r = app()->make('role_repository');
        parent::__construct($request);
    }

    public function index(Request $request)
    {
        $per_page = (int) $request->input('per_page', 12);
        $data = RoleRepo::all($per_page);

        return $this->paginate($data, new RoleTransformer(), 1, '获取成功');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        RoleRepo::validate($data);

        $role = RoleRepo::create($data);

        return $this->item($role, new RoleTransformer(), 1, '创建成功');
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $role = RoleRepo::update($id, $data);

        return $this->item($role, new RoleTransformer(), 1, '修改成功');
    }

    public function show(Request $request, $id)
    {
        $role = RoleRepo::getModel()->with('permissions')->find($id);

        return $this->item($role, new RoleTransformer(), 1, '获取成功');
    }

    public function delete(Request $request, $id)
    {
        $role = RoleRepo::delete($id);

        return $this->item($role, new RoleTransformer(), 1, '删除成功');
    }

    /**
     * 指定角色增加权限.
     *
     * @param Request $request [description]
     * @param [type]  $id      [description]
     */
    public function addPermissions(Request $request, $id)
    {
        $role = RoleRepo::find($id);

        $str = $request->input('ids', '');
        $permission_ids = parse_ids_from_str($str);
        RoleRepo::addPermissions($id, $permission_ids);

        return $this->item($role, new RoleTransformer(), 1, '添加成功');
    }
}
