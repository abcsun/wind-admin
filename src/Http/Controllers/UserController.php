<?php

namespace Wind\Http\Controllers;

use App;
use Event;
use Illuminate\Http\Request;
use Wind\Facades\UserRepository as UserRepo;
use Wind\Transformers\UserTransformer;
use Wind\Events\UserRoleRelationChangedEvent;

// use Tymon\JWTAuth\Exceptions\JWTException;
// use Tymon\JWTAuth\Exceptions\TokenExpiredException;
// use Overtrue\Socialite\SocialiteManager;

/**
 * 用户控制器.
 *
 * @author scl <scl@winhu.com>
 */
class UserController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->r = app()->make('user_repository');
        parent::__construct($request);
    }

    /**
     * 获取用户可公开基本信息.
     */
    public function show(Request $request, $id)
    {
        $user = UserRepo::find($id);

        return $this->item($user, new UserTransformer(), 1, '获取成功');
    }

    /**
     * 用户列表数据，使用per_page指定每页显示数量.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function index(Request $request)
    {
        $per_page = (int) $request->input('per_page', 12);
        if ($users = UserRepo::all($per_page)) {
            return $this->paginate($users, new UserTransformer(), 1, '获取成功');
        } else {
            return response_json(0, '', '获取失败');
        }
    }

    /**
     * 创建新的用户.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $v = UserRepo::validate($data, ['phone', 'name', 'password']);

        $data['open_id'] = generate_student_id();
        $user = UserRepo::create($data);

        return $this->item($user, new UserTransformer(), 1, '创建成功');
    }

    /**
     * 用户信息更新.
     *
     * @param Request $request [description]
     * @param [type]  $id      [description]
     *
     * @return [type] [description]
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $v = UserRepo::validate($data, ['name']);

        if ($user = UserRepo::updateByUser($id, $data, $this->user, 'id')) {
            return $this->item($user, new UserTransformer(), 1, '修改成功');
        } else {
            return response_json(0, '', '修改失败', 404);
        }
    }

    /**
     * 用户删除.
     *
     * @param [type] $id [description]
     *
     * @return [type] [description]
     */
    public function delete(Request $request, $id)
    {
        if ($user = UserRepo::deleteByUser($id, $this->user, 'id')) {
            return $this->item($user, new UserTransformer(), 1, '删除成功');
        } else {
            return response_json(0, '', '删除失败', 404);
        }
    }

    /**
     * 获取登录用户的已授权菜单.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function getUserMenu(Request $request)
    {
        $user_id = $this->user->id;
        $data = UserRepo::generateMenuByUser($user_id)->toArray();
        $menus = list_to_tree($data);
        foreach ($data as $permission) {
            $grant_routes[] = $permission['slug'];
        }

        return response_json(1, compact('menus', 'grant_routes'), 'success');
    }

    /**
     * 用户增加角色.
     *
     * @param Request $request [description]
     * @param [type]  $id      [description]
     */
    public function addRoles(Request $request, $id)
    {
        $user = UserRepo::find($id);

        $str = $request->input('ids', '');
        $role_ids = parse_ids_from_str($str);
        UserRepo::addRoles($id, $role_ids);

        // 触发用户角色关系更改事件
        Event::fire(new UserRoleRelationChangedEvent($user));

        return $this->item($user, new UserTransformer(), 1, '添加成功');
    }
}
