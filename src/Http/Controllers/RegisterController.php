<?php

namespace Wind\Http\Controllers;

// use Tymon\JWTAuth\Exceptions\JWTException;
// use Tymon\JWTAuth\Exceptions\TokenExpiredException;
// use Overtrue\Socialite\SocialiteManager;
use Illuminate\Http\Request;
use Wind\Facades\UserRepository as UserRepo;
use JWTAuth;

/**
 * 注册控制器.
 *
 * @author scl <scl@winhu.com>
 */
class RegisterController extends BaseController
{
    public function __construct()
    {
    }

    /**
     * 注册接口.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function register(Request $request)
    {
        $data = $request->all();
        UserRepo::validate($data, ['phone', 'code', 'password']);

        //验证码校验
        if (!verify_code_with_phone($data['code'], $data['phone'])) {
            // return response_json(0, '验证码错误', '验证码错误');
        }

        return $this->registerProcess($data, Config('sys.DEFAULT_TOKEN_TTL'));
    }

    /**
     * 注册处理公共函数.
     *
     * @param [type] $input [description]
     *
     * @return [type] [description]
     */
    public function registerProcess($input, $token_ttl = 86400)
    {
        if ($user = UserRepo::canRegisterBy('phone', $input['phone'])) {
            //cann't register
            return response_json(0, 'http://login', '该用户已注册，请直接登录');
        }

        // $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
        $input['open_id'] = generate_student_id(); //生成学生id;
        if ($user = UserRepo::create($input)) {
            $token = JWTAuth::fromUser($user);

            return response_json(1, $token, '注册成功'); //  返回一个验证token
        } else {
            return response_json(1, '注册失败', '注册失败');
        }
    }
}
