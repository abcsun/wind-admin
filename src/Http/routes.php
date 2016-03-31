<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

$app->get('/api/v1/helloworld', function () use ($app) {
    return response()->json(['msg' => 'helloworld']);
});
// var_dump(app('api.exception'));

// API Routes
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'Wind\Http\Controllers',
    // 'middleware' => ['acl'],
    ], function ($api) {
    //测试API
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('/hi', function () {
            echo 'hello dingo';

            return config_path();
        });
    });
    //测试API
    $api->group(['prefix' => 'v1/test',
        'middleware' => ['api.auth', 'acl'],
        'providers' => ['jwt', 'basic', 'oauth'],
    ], function ($api) {
        $api->get('/api1', [
            'as' => 'test.api1',
            function () {
                return response_json(1, 'test.api1', 'success');
            },
        ]);
        $api->get('/api2', [
            'as' => 'test.api2',
            function () {
                return response_json(1, 'test.api2', 'success');
            },
        ]);
        $api->get('/api3', [
            'as' => 'test.api3',
            function () {
                return response_json(1, 'test.api3', 'success');
            },
        ]);

    });

    //系统服务类API
    $api->group(['prefix' => 'v1/system',
        // 'middleware' => ['api.auth', 'acl'],
        'providers' => ['jwt', 'basic', 'oauth'],
    ], function ($api) {
        $api->delete('/cache/{tag}', [
            'as' => 'system.delete.cache',
            'uses' => 'SystemController@flushCacheByTag',
        ]);

    });

    //配置管理API
    $api->group(['prefix' => 'v1/config',
        'middleware' => ['api.auth', 'acl'],
        'providers' => ['jwt', 'basic', 'oauth'],
        ], function ($api) {
        $api->get('/', [
            'as' => 'config.index',
            'uses' => 'ConfigController@index',
        ]);
        $api->post('/', [
            'as' => 'config.store',
            'uses' => 'ConfigController@store',
        ]);
        $api->put('/', [
            'as' => 'config.batch.update',
            'uses' => 'ConfigController@batchUpdate',
        ]);
        $api->delete('/', [
            'as' => 'config.batch.delete',
            'uses' => 'ConfigController@batchSoftDelete',
        ]);
        $api->get('/{id:[0-9]+}', [
            'as' => 'config.show',
            'uses' => 'ConfigController@show',
        ]);
        $api->put('/{id:[0-9]+}', [
            'as' => 'config.update',
            'uses' => 'ConfigController@update',
        ]);
        $api->delete('/{id:[0-9]+}', [
            'as' => 'config.delete',
            'uses' => 'ConfigController@delete',
        ]);
        $api->put('/{batch_operation}', [
            'as' => 'config.batch.operation',
            'uses' => 'ConfigController@batchOperation',
        ]);
    });

    // 用户组验证API
    $api->group(['prefix' => 'v1/user',
        'middleware' => ['api.auth', 'acl'],
        'providers' => ['jwt', 'basic', 'oauth'],
        ], function ($api) {
        // $api->get('/menu', [
        //     'as' => 'user.menu',
        //     'uses' => 'UserController@getUserMenu',
        // ]);
        $api->get('/', [
            'as' => 'user.index',
            'uses' => 'UserController@index',
        ]);
        $api->post('/', [
            'as' => 'user.store',
            'uses' => 'UserController@store',
        ]);
        $api->put('/', [
            'as' => 'user.batch.update',
            'uses' => 'UserController@batchUpdate',
        ]);
        $api->delete('/', [
            'as' => 'user.batch.delete',
            'uses' => 'UserController@batchSoftDelete',
        ]);
        $api->get('/{id:[0-9]+}', [
            'as' => 'user.show',
            'uses' => 'UserController@show',
        ]);
        $api->put('/{id:[0-9]+}', [
            'as' => 'user.update',
            'uses' => 'UserController@update',
        ]);
        $api->delete('/{id:[0-9]+}', [
            'as' => 'user.delete',
            'uses' => 'UserController@delete',
        ]);
        $api->post('/{id:[0-9]+}/role', [
            'as' => 'user.add.role',
            'uses' => 'UserController@addRoles',
        ]);
        $api->put('/{batch_operation}', [
            'as' => 'user.batch.operation',
            'uses' => 'UserController@batchOperation',
        ]);
    });

    // GrantApi组验证API
    $api->group(['prefix' => 'v1/api',
        'middleware' => ['api.auth', 'acl'],
        'providers' => ['jwt', 'basic', 'oauth'],
        ], function ($api) {
        $api->get('/', [
            'as' => 'api.index',
            'uses' => 'GrantApiController@index',
        ]);
        $api->post('/', [
            'as' => 'api.store',
            'uses' => 'GrantApiController@store',
        ]);
        $api->put('/', [
            'as' => 'api.batch.update',
            'uses' => 'GrantApiController@batchUpdate',
        ]);
        $api->delete('/', [
            'as' => 'api.batch.delete',
            'uses' => 'GrantApiController@batchSoftDelete',
        ]);
        $api->get('/{id:[0-9]+}', [
            'as' => 'api.show',
            'uses' => 'GrantApiController@show',
        ]);
        $api->put('/{id:[0-9]+}', [
            'as' => 'api.update',
            'uses' => 'GrantApiController@update',
        ]);
        $api->delete('/{id:[0-9]+}', [
            'as' => 'api.delete',
            'uses' => 'GrantApiController@delete',
        ]);
        $api->put('/{batch_operation}', [
            'as' => 'api.batch.operation',
            'uses' => 'GrantApiController@batchOperation',
        ]);
    });

    // 权限组验证API
    $api->group(['prefix' => 'v1/permission',
        'middleware' => ['api.auth', 'acl'],
        'providers' => ['jwt', 'basic', 'oauth'],
        ], function ($api) {
        $api->post('/update_sort', [
            'as' => 'permission.update_sort',
            'uses' => 'PermissionController@updatePermissionSort',
        ]);
        $api->get('/', [
            'as' => 'permission.index',
            'uses' => 'PermissionController@index',
        ]);
        $api->post('/', [
            'as' => 'permission.store',
            'uses' => 'PermissionController@store',
        ]);
        $api->put('/', [
            'as' => 'permission.batch.update',
            'uses' => 'PermissionController@batchUpdate',
        ]);
        $api->delete('/', [
            'as' => 'permission.batch.delete',
            'uses' => 'PermissionController@batchSoftDelete',
        ]);
        $api->get('/{id:[0-9]+}', [
            'as' => 'permission.show',
            'uses' => 'PermissionController@show',
        ]);
        $api->put('/{id:[0-9]+}', [
            'as' => 'permission.update',
            'uses' => 'PermissionController@update',
        ]);
        $api->delete('/{id:[0-9]+}', [
            'as' => 'permission.delete',
            'uses' => 'PermissionController@delete',
        ]);
        $api->post('/{id:[0-9]+}/api', [
            'as' => 'permission.add.api',
            'uses' => 'PermissionController@addGrantApis',
        ]);
        $api->put('/{batch_operation}', [
            'as' => 'permission.batch.operation',
            'uses' => 'PermissionController@batchOperation',
        ]);
    });

    // 角色组验证API
    $api->group(['prefix' => 'v1/role',
        'middleware' => ['api.auth', 'acl'],
        'providers' => ['jwt', 'basic', 'oauth'],
        ], function ($api) {
        $api->get('/', [
            'as' => 'role.index',
            'uses' => 'RoleController@index',
        ]);
        $api->post('/', [
            'as' => 'role.store',
            'uses' => 'RoleController@store',
        ]);
        $api->put('/', [
            'as' => 'role.batch.update',
            'uses' => 'RoleController@batchUpdate',
        ]);
        $api->delete('/', [
            'as' => 'role.batch.delete',
            'uses' => 'RoleController@batchSoftDelete',
        ]);
        $api->get('/{id:[0-9]+}', [
            'as' => 'role.show',
            'uses' => 'RoleController@show',
        ]);
        $api->put('/{id:[0-9]+}', [
            'as' => 'role.update',
            'uses' => 'RoleController@update',
        ]);
        $api->delete('/{id:[0-9]+}', [
            'as' => 'role.delete',
            'uses' => 'RoleController@delete',
        ]);
        $api->post('/{id:[0-9]+}/permission', [
            'as' => 'role.add.permission',
            'uses' => 'RoleController@addPermissions',
        ]);
        $api->put('/{batch_operation}', [
            'as' => 'role.batch.operation',
            'uses' => 'RoleController@batchOperation',
        ]);
    });

    // 账户组开放api
    $api->group(['prefix' => 'v1/account'], function ($api) {
        $api->post('/register', [
            'as' => 'account.register',
            'uses' => 'RegisterController@register',
        ]);
        $api->post('/login', [
            'as' => 'account.login',
            'uses' => 'LoginController@login',
        ]);
    });

    // 账户组验证API
    $api->group(['prefix' => 'v1/account',
        'middleware' => ['api.auth'],
        'providers' => ['jwt', 'basic', 'oauth'],
        ], function ($api) {
        $api->get('/menu', [
            'as' => 'account.menu',
            'uses' => 'AccountController@getUserMenu',
        ]);
        $api->get('/probe_login', [
            'as' => 'account.probe_login',
            'uses' => 'AccountController@probeLogin',
        ]);
        $api->put('/password/reset', [
            'as' => 'account.password.reset',
            'uses' => 'AccountController@resetPassword',
        ]);
        $api->put('/password/forget', [
            'as' => 'account.password.forget',
            'uses' => 'AccountController@probeLogin',
        ]);
        $api->get('/logout', [
            'as' => 'account.logout',
            'uses' => 'LoginController@logout',
        ]);
    });

    //操作日志
    $api->group(['prefix' => 'v1/log',
        'middleware' => ['api.auth', 'acl'],
        'providers' => ['jwt', 'basic', 'oauth'],
    ], function ($api) {
        $api->get('/', [
            'as' => 'log.index',
            'uses' => 'RevisionController@index',
        ]);
        $api->delete('/all', [
            'as' => 'log.all',
            'uses' => 'RevisionController@clear',
        ]);
        $api->delete('/', [
            'as' => 'log.delete',
            'uses' => 'RevisionController@delete',
        ]);

    });

});
