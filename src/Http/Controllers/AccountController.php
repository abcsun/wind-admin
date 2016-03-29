<?php

namespace Wind\Http\Controllers;

use Illuminate\Http\Request;
use Wind\Facades\UserRepository as UserRepo;

/**
 * 用户账号控制器.
 *
 * @author scl <scl@winhu.com>
 */
class AccountController extends BaseController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * 检测用户是否登录，并返回基本信息.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function probeLogin(Request $request)
    {
        return response_json(1, $this->user, 'success');
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
     * 重置密码
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function resetPassword(Request $request)
    {
        $input = $request->only('password', 'old_password');

        //新密码格式验证
        UserRepo::validate($input, ['password']);

        //旧密码错误
        $user_detail = UserRepo::find($this->user->id); //由于缓存的用户中没有密码，因此重新获取一次
        if (!password_verify($input['old_password'], $user_detail['password'])) {
            return response_json(0, '原密码错误', '原密码错误');
        }

        return $this->updatePasswordProcess($new_pass);
    }

    /**
     * 密码更新共有处理.
     *
     * @param [type] $phone    [description]
     * @param [type] $new_pass [description]
     *
     * @return [type] [description]
     */
    public function updatePasswordProcess($new_pass)
    {
        $user = UserRepo::update($this->user->id, ['password' => $new_pass]);

        return response_json(1, $user->password, '密码设置成功');
    }
}
