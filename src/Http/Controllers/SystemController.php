<?php

namespace Wind\Http\Controllers;

use Cache;
use Illuminate\Http\Request;

/**
 * 系统服务控制器.
 *
 * @author scl <scl@winhu.com>
 */
class SystemController extends BaseController
{
    public function __construct()
    {
    }

    /**
     * 用户登录.
     * 
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    /**
     * 情况指定tag的缓存.
     *
     * @param Request $request [description]
     * @param string  $tag     默认为system
     *
     * @return [type] [description]
     */
    public function flushCacheByTag(Request $request, $tag = 'system')
    {
        Cache::tags($tag)->flush();

        return response_json(1, '', '清空成功');
    }
}
