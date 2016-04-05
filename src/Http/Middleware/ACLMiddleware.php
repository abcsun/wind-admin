<?php

namespace Wind\Http\Middleware;

use Wind\Facades\UserRepository as UserRepo;

/**
 * @author scl <scl@winhu.com>
 */
class ACLMiddleware
{
    /**
     * 所有有效token都会通过.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $api_slug = $request->route()[1]['as']; //当前处理路由的slug
        $user_id = $request->user()->id;
        if (C('API_ACL')) {
            UserRepo::assertAccess($user_id, $api_slug);
        }

        return $next($request);  //继续下一环节处理
    }
}
