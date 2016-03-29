<?php

namespace Wind\Http\Controllers;

use App;
use Event;
use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
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
        Event::fire(new UserRoleRelationChangedEvent($id));

        return $this->item($user, new UserTransformer(), 1, '添加成功');
    }

// 以下为xtx当中代码，暂时备份
    /**
     * 初始化密码，用于手机端注册时.
     *
     * @return json
     */
    public function initPassword()
    {
        if (!$user = $this->CheckUserByToken()) {
            return response_json(0, trans('message.user_auth_fail'), trans('message.user_auth_fail'));
        }
        $phone = $user['phone'];
        $new_pass = Request::input('password');
        //验证新密码格式合法性
        if (!$this->v->validatePassword($new_pass)) {
            return response_json(-1, $this->v->getErrors(), '验证失败');
        }
        $user_detail = $this->r->find($user['id']); //由于缓存的用户中没有密码，因此重新获取一次
        if ($user_detail->password == '') {
            //原始密码为空
            return $this->updatePasswordByPhoneProcess($phone, $new_pass);
        } else {
            return response_json(0, '无法初始化密码', '无法初始化密码');
        }
    }

    /**
     * 忘记密码后根据验证码重新设置新密码
     * 两种情况：无password时只校验验证码是否正确
     * 有password时验证码正确后修改密码
     *
     * @return [type] [description]
     */
    public function forgetPassword()
    {
        $code = Request::input('code');
        $phone = Request::input('phone');
        $new_pass = Request::input('password', null);

        if (!verify_code_with_phone($code, $phone, $flush = false)) {
            return response_json(0, '验证码错误', '验证码错误');
        } else {
            if (is_null($new_pass)) {
                return response_json(1, '', '验证码正确');
            }
        }

        if (!$this->v->validatePassword($new_pass)) {
            return response_json(-1, $this->v->getErrors(), '验证失败');
        }

        return $this->updatePasswordByPhoneProcess($phone, $new_pass);
    }

    /**
     * 根据原有密码设置新密码
     *
     * @return [type] [description]
     */
    public function resetPassword()
    {
        if (!$user = $this->CheckUserByToken()) {
            return response_json(0, trans('message.user_auth_fail'), trans('message.user_auth_fail'));
        }

        $new_pass = Request::input('password');
        $old_pass = Request::input('old_password');

        //新密码格式验证
        if (!$this->v->validatePassword($new_pass)) {
            return response_json(-1, $this->v->getErrors(), '验证失败');
        }
        //旧密码错误
        $user_detail = $this->r->find($user['id']); //由于缓存的用户中没有密码，因此重新获取一次
        if (!password_verify($old_pass, $user_detail['password'])) {
            return response_json(0, '原密码错误', '原密码错误');
        }

        return $this->updatePasswordByPhoneProcess($user['phone'], $new_pass);
    }

    /**
     * 密码更新共有处理.
     *
     * @param [type] $phone    [description]
     * @param [type] $new_pass [description]
     *
     * @return [type] [description]
     */
    public function updatePasswordByPhoneProcess($phone, $new_pass)
    {
        $user_id = $this->r->updatePasswordByPhone($phone, $new_pass);
        switch ($user_id) {
            case -1:
                return response_json(0, '新密码不能与旧密码相同', '新密码不能与旧密码相同');
                break;
            case -2:
                return response_json(0, '密码重置失败，请重试', '密码重置失败，请重试');
            case 0:
                return response_json(0, '无注册信息', '无注册信息');
                break;
            default:

        }

        try {
            $user_data = $this->generateToken($user_id, Config('sys.TOKEN_TTL'));

            return response_json(1, $user_data, '密码设置成功');
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response_json(2, '创建token失败', '创建token失败');
        }
    }

    /**
     * 第三方用户登录注册.
     *
     * @param [type] $type [description]
     *
     * @return [type] [description]
     */
    public function loginFromSocial($type)
    {
        if (!in_array($type, Config('socialite.allowed_sites'))) {
            return response_json(-1, '', '异常的登录参数');
        }

        $socialite = new SocialiteManager(Config('socialite.config'));
        $user = $socialite->driver($type)->user();
        $social_data = array(
            'type' => Config("socialite.sites_type.$type"),
            'open_id' => $user->getId(),
        );
        $open_user = SocialiteModel::where($social_data)->select('user_id', 'open_id', 'name', 'gravatar', 'type')->first();
        $user_repo = App::make('user_repository');

        if ($open_user) {
            //第三方登录过
            $user_id = $open_user->user_id;
            if (!$user_id) {
                $open_user->randomstr = bind_randomstr_to_social_user($type, $social_data['open_id']); //绑定一个随机串用于已登录第三方用户再次确认
                return response_json(2, $open_user, '未绑定手机信息');
            }
        } else {
            //创建来自第三方登录的信息
            $social_data['user_id'] = 0;
            $social_data['name'] = $user->getName() ?: $user->getNickname ?: '';
            $social_data['gravatar'] = $user->getAvatar() ?: '';
            $open_user = SocialiteModel::create($social_data);
            $open_user->randomstr = bind_randomstr_to_social_user($type, $social_data['open_id']); //绑定一个随机串用于已登录第三方用户再次确认
            return response_json(2, $open_user, '未绑定手机信息');
        }
        $user_data = $this->generateToken($user_id, Config('sys.DEFAULT_TOKEN_TTL'));

        return response_json(1, $user_data, '登陆成功');
    }

    /**
     * 绑定第三方用户
     * 输入参数包括open_id以及正确的randomstr.
     *
     * @param [type] $type [description]
     *
     * @return [type] [description]
     */
    public function bindSocialUser($type)
    {
        if (!$user = $this->CheckUserByToken()) {
            return response_json(0, trans('message.user_auth_fail'), trans('message.user_auth_fail'));
        }

        if (!in_array($type, Config('socialite.allowed_sites'))) {
            return response_json(-1, '', '异常的绑定参数');
        }

        $data = Request::only('randomstr', 'open_id');
        if (!$this->v->validateBindSocialUser($data)) {
            return response_json(-1, $this->v->getErrors(), '验证失败');
        }

        if (!verify_randomstr_to_social_user($type, $data['open_id'], $data['randomstr'])) {
            return response_json(0, '', '未授权的第三方用户');
        }

        $user_data = array(
            'type' => Config("socialite.sites_type.$type"),
            'user_id' => $user['id'],
        );
        if (SocialiteModel::where($user_data)->first()) {
            return response_json(0, '', '您已绑定过此类社交账户');
        }

        $social_data = array(
            'type' => Config("socialite.sites_type.$type"),
            'open_id' => $data['open_id'],
        );
        $open_user = SocialiteModel::where($social_data)->first();
        if ($open_user->user_id) {
            return response_json(0, '', '此社交账号已被绑定过');
        }
        $open_user->user_id = $user['id'];
        $open_user->save();

        //当前用户无name时使用第三方用户昵称代替
        if ($user['name'] == '') {
            $user_update['name'] = $open_user->name;
            if (strlen($user['gravatar']) < 18) {
                //默认头像default_gravatar时保存第三方头像
                $fm = new PublicFileManageController();
                $key = $fm->fetchFileToQiniu($open_user->gravatar);
                if ($key) {
                    $user_update['gravatar'] = $open_user->gravatar;
                }
            }
            $this->r->updateInfo($user['id'], $user_update);
        }

        return response_json(1, $open_user, '绑定成功');
    }

    /**
     * 我的已绑定社交账户列表.
     *
     * @return [type] [description]
     */
    public function mySocialUser()
    {
        if (!$user = $this->CheckUserByToken()) {
            return response_json(0, trans('message.user_auth_fail'), trans('message.user_auth_fail'));
        }

        $config = Config('socialite.binding_format');
        $sites = Config('socialite.sites_name');
        $res = SocialiteModel::where('user_id', $user['id'])->select('id', 'name', 'gravatar', 'type', 'updated_at as binding_at')->get()->toArray();
        foreach ($res as $key => $value) {
            $value['type_name'] = $sites[$value['type']];
            $config[$value['type']] = $value;
        }

        return response_json(1, $config, '获取成功');
    }
    /**
     * 解除社交账号绑定关系.
     *
     * @param [type] $id [description]
     *
     * @return [type] [description]
     */
    public function unbindSocialUser($id)
    {
        if (!$user = $this->CheckUserByToken()) {
            return response_json(0, trans('message.user_auth_fail'), trans('message.user_auth_fail'));
        }
        $conditions = array(
            'user_id' => $user['id'],
            'id' => $id,
        );
        if ($res = SocialiteModel::where($conditions)->delete()) {
            return response_json(1, $res, '解除成功');
        }

        return response_json(0, '', '无绑定关系，解除失败');
    }

    /**
     * 生成用户token，并且保存到cache中.
     *
     * @param [type] $user_id   [description]
     * @param [type] $token_ttl [description]
     *
     * @return [type] [description]
     */
    public function generateToken($user_id, $token_ttl)
    {
        //生成token
        $claims = [
            'sub' => $user_id,
            'exp' => time() + $token_ttl,
        ];
        $payload = JWTFactory::make($claims);
        $token = JWTAuth::encode($payload)->get();

        $user_repo = App::make('user_repository');
        $info = $user_repo->getUserInfo($user_id);

        cache_user_token($user_id, $token, $info); //缓存用户登录token及基本信息

        $user_data = array(
            'token' => $token,
            'user_info' => $info, //获取个人基本信息,
        );

        return $user_data;
    }

    /**
     * 刷新用户token，主要用于token过期后的登录状态延续.
     *
     * @return [type] [description]
     */
    public function refreshToken()
    {
        $token = JWTAuth::getToken();
        // $jwt_manager = App::make('tymon.jwt.manager');
        $jwt = App::make('tymon.jwt.provider.jwt');

        try {
            $payload = $jwt->decode($token);
        } catch (JWTException $e) {
            return response_json(0, '', 'token异常');
        }

        // $user = $this->r->find($payload['sub']); //sub => user_id
        // $claims = ['exp' => time() + Config('sys.DEFAULT_TOKEN_TTL')];
        // $token = JWTAuth::fromUser($user, $claims);
        $user_data = $this->generateToken($user_id, Config('sys.DEFAULT_TOKEN_TTL'));

        return response_json(1, $user_data['token'], 'token刷新成功');
    }
    /**
     * 根据token获取用户.
     *
     * @return json
     */
    public function getByToken()
    {
        if (!$user = $this->CheckUserByToken()) {
            return response_json(0, trans('message.user_auth_fail'), trans('message.user_auth_fail'));
        }

        if ($user && ($info = $this->r->getUserInfo($user['id']))) {
            return response_json(1, $info, '获取成功');
        } else {
            return response_json(0, $info, '获取失败');
        }
    }

    /**
     * 探测用户是否登录.
     *
     * @return [type] [description]
     */
    public function probeLogin()
    {
        return response_json(1, '已登录', '已登录');
    }
}
