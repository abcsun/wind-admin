<?php

namespace Wind\Http\Controllers;

use Illuminate\Http\Request;
use Wind\Facades\GrantApiRepository as ApiRepo;
use Wind\Transformers\GrantApiTransformer;

/**
 * API控制器.
 *
 * @author scl <scl@winhu.com>
 */
class GrantApiController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->r = app()->make('grant_api_repository');
        parent::__construct($request);
    }

    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 15);
        $apis = ApiRepo::all($per_page);

        return $this->paginate($apis, new GrantApiTransformer(), 1, '获取成功');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        ApiRepo::validate($data);

        $apis = ApiRepo::create($data);

        return $this->item($apis, new GrantApiTransformer(), 1, '创建成功');
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $apis = ApiRepo::update($id, $data);

        return $this->item($apis, new GrantApiTransformer(), 1, '修改成功');
    }

    public function show(Request $request, $id)
    {
        $apis = ApiRepo::find($id);

        return $this->item($apis, new GrantApiTransformer(), 1, '获取成功');
    }

    public function delete(Request $request, $id)
    {
        $apis = ApiRepo::delete($id);

        return $this->item($apis, new GrantApiTransformer(), 1, '删除成功');
    }
}
