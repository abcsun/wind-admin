<?php

namespace Wind\Http\Middleware;

use Config;
use Cache;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use JWTAuth;

/**
 * 根据JWT的token解析并验证用户信息的中间件
 * 1 token验证通过触发JWTValidEvent时间
 * 2 异常时返回70*响应错误.
 *
 * @author scl <scl@winhu.com>
 */
class GetUserFromTokenMiddleware
{
    /**
     * 测试使用
     * 所有有效token都会通过.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // var_dump($reqst);
        if (!$token = JWTAuth::getToken()) {
            return response()->json(['code' => 700, 'result' => '', 'message' => '缺少token'])->header('Status', 400);
        }

        try {
            //TODO:此处token验证可考虑缓存后加生存时间验证，用于处理token过期
            $user = JWTAuth::parseToken()->authenticate();
            dd($user);

            // if(!$user){
            //     return response()->json(['code'=>701, 'result'=>'', 'message'=>'用户信息错误'])->header('Status', 404);
            // }else{
                return $next($request);  //继续下一环节处理
            // }
        } catch (TokenExpiredException $e) {
            return response()->json(['code' => 702, 'result' => '', 'message' => 'token过期'])->header('Status', 702);
        } catch (JWTException $e) {
            return response()->json(['code' => 703, 'result' => '', 'message' => 'token异常'])->header('Status', $e->getStatusCode());
        }
    }

    /**
     * 检测用户token，主要流程.
     * 
     * token是否已缓存
     *   是：是否该用户的最近使用token
     *       无最近token：刷新该用户的最近唯一token
     *       否：刷掉当前token，返回错误信息
     *       是：继续处理
     *       
     * @param [type] $token [description]
     *
     * @return [type] [description]
     */
    public function handle1($request, \Closure $next)
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json(['code' => 700, 'result' => '', 'message' => '缺少token']);//->header('Status', 400);
        }

        $md5_token = md5($token);
        $md5_token_key = Config('sys.USER_CACHE_PREFIX').$md5_token;
        if ($user = Cache::get($md5_token_key)) { //token已缓存，且为最新登陆的token
            $last_token_key = Config('sys.USER_LAST_TOKEN_CACHE_PREFIX').$user['id'];
            $last_token = Cache::get($last_token_key);

            if ($last_token === null) { //无上个有效token
                Cache::put($last_token_key, $md5_token, 14400); //user_id=>last_avail_token
            } elseif ($last_token !== $md5_token) { //非最新登陆token时刷掉缓存
                Cache::forget(Config('sys.USER_CACHE_PREFIX').$md5_token);

                return response()->json(['code' => 704, 'result' => '', 'message' => 'token已失效']);
            }

            return $next($request);  //继续下一环节处理
        } else { //新token
            return response()->json(['code' => 702, 'result' => '', 'message' => '非正常生产token']);
        }
    }
}
