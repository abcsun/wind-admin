<?php

namespace Wind\Http\Controllers;

use Auth;
use JWTAuth;
use Illuminate\Http\Request;
// use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * 登录控制器.
 *
 * @author scl <scl@winhu.com>
 */
class LoginController extends BaseController
{
    // private $auth;

    public function __construct()
    {
        // $this->auth = $auth;
    }

    /**
     * 用户登录.
     * 
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function login(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('phone', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return response_json(0, 'phone或者password有误', '登录失败');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response_json(0, '创建TOKEN失败', '登录失败');
        }

        // all good so return the token
        return response_json(1, $token, '登录成功');
    }

    /**
     * 用户注销登录.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        return response_json(1, $user, '注销成功');
    }
}
