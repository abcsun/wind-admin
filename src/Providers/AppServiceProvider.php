<?php

namespace Wind\Providers;

use DB;
use Log;
use Illuminate\Support\ServiceProvider;
use Wind\Http\Middleware\GetUserFromTokenMiddleware;
use Wind\Models\RevisionModel;
use Wind\Models\UserModel;
use Wind\Models\RoleModel;
use Wind\Models\PermissionModel;
use Wind\Models\GrantApiModel;
use Wind\Repositories\RevisionRepository;
use Wind\Repositories\UserRepository;
use Wind\Repositories\RoleRepository;
use Wind\Repositories\PermissionRepository;
use Wind\Repositories\GrantApiRepository;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Auth\Auth as DingoAuth;
use Dingo\Api\Auth\Provider\JWT as JWTProvider;
use Wind\Models\ConfigModel;
use Wind\Repositories\ConfigRepository;
use Wind\Http\Middleware\ACLMiddleware;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Wind\Providers\MySQLHandlerProvider as MySQLHandler;

/**
 * Wind应用服务提供者.
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->group(['namespace' => 'Wind\Http\Controllers'], function ($app) {
            require __DIR__.'/../Http/routes.php';
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        load_config_to_cache(); //加载所有的配置到缓存中

        //是否记录SQL开关
        if (C('DEBUG_SQL')) {
            $this->enableQueryLog();
        }
        // 是否使用数据库集中记录日志
        if (!C('MONOLOG_DB')) {
            $this->enableMonologDB();
        }

        $this->registerJWTAPIAuth();

        $this->registerMiddlewares();
        $this->registerUserRepository();
        $this->registerRoleRepository();
        $this->registerConfigRepository();
        $this->registerPermissionRepository();
        $this->registerGrantApiRepository();
        $this->registerRevisionRepository();
    }

    /**
     * 注册api.auth中的jwt认证
     *
     * @return [type] [description]
     */
    public function registerJWTAPIAuth()
    {
        $this->app->extend('api.auth', function (DingoAuth $auth) {
            $auth->extend('jwt', function ($app) {
                return new JWTProvider($app[JWTAuth::class]);
            });

            return $auth;
        });
    }

    /**
     * 使用数据库记录日志.
     *
     * @return [type] [description]
     */
    public function enableMonologDB()
    {
        $this->app->configureMonologUsing(function (\Monolog\Logger $logger) {
            // 保存到数据库的handler
            $pdo = new \PDO('mysql:host=192.168.1.100;dbname=db_log', 'root', '123456');
            $mySQLHandler = new MySQLHandler($pdo, 'logs', array('app', 'user'), Logger::DEBUG);
            $logger->pushHandler($mySQLHandler);

            //保留lumen自带log文件
            $origin = (new StreamHandler(storage_path('logs/lumen.log'), Logger::DEBUG))
                                     ->setFormatter(new LineFormatter(null, null, true, true));
            $logger->pushHandler($origin);

            return $logger;
        });
    }
    /**
     * 记录sql查询记录.
     *
     * @return [type] [description]
     */
    public function enableQueryLog()
    {
        // Create the logger
        $logger = new Logger('sql');

        // Create the handler
        $logger->pushHandler(new StreamHandler(storage_path('logs'.DIRECTORY_SEPARATOR.date('Y-m-d').'_query.log')), Logger::INFO);

        DB::listen(
            function ($sql) use ($logger) {
                // $sql is an object with the properties:
                //  sql: The query
                //  bindings: the sql query variables
                //  time: The execution time for the query
                //  connectionName: The name of the connection

                // To save the executed queries to file:
                // Process the sql and the bindings:
                foreach ($sql->bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $sql->bindings[$i] = "'$binding'";
                        }
                    }
                }
                // Insert bindings into query
                $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

                $query = vsprintf($query, $sql->bindings).' time: '.$sql->time.' ms';

                $logger->addDebug($query); //save the query to query.log
                Log::debug($query);
            }
        );
    }

    /**
     * 注册中间件.
     *
     * @return [type] [description]
     */
    public function registerMiddlewares()
    {
        $this->app->routeMiddleware([
            'GetUserFromToken' => GetUserFromTokenMiddleware::class,
            // 'auth' => App\Http\Middleware\Authenticate::class,
            'acl' => ACLMiddleware::class,
        ]);
    }

    /**
     * Register the user repository class.
     */
    protected function registerUserRepository()
    {
        $this->app->singleton('user_repository', function ($app) {
            $model = new UserModel();
            $validator = $app['validator'];

            return new UserRepository($model, $validator);
        });

        $this->app->alias('user_repository', UserRepository::class);
    }

    protected function registerConfigRepository()
    {
        $this->app->singleton('config_repository', function ($app) {
            $model = new ConfigModel();
            $validator = $app['validator'];

            return new ConfigRepository($model, $validator);
        });

        $this->app->alias('ConfigRepository', ConfigRepository::class);
    }

    /**
     * Register the permission repository class.
     */
    protected function registerPermissionRepository()
    {
        $this->app->singleton('permission_repository', function ($app) {
            $model = new PermissionModel();
            $validator = $app['validator'];

            return new PermissionRepository($model, $validator);
        });
        // $this->app->alias('permission_repository', PermissionRepository::class);
    }

    /**
     * Register the role repository class.
     */
    protected function registerRoleRepository()
    {
        $this->app->singleton('role_repository', function ($app) {
            $model = new RoleModel();
            $validator = $app['validator'];

            return new RoleRepository($model, $validator);
        });

        // $this->app->alias('role_repository', RoleRepository::class);
    }

    /**
     * Register the grant api repository class.
     */
    protected function registerGrantApiRepository()
    {
        $this->app->singleton('grant_api_repository', function ($app) {
            $model = new GrantApiModel();
            $validator = $app['validator'];

            return new GrantApiRepository($model, $validator);
        });
    }

    /**
     * Register the revision repository class.
     */
    protected function registerRevisionRepository()
    {
        $this->app->singleton('revision_repository', function ($app) {
            $model = new RevisionModel();
            $validator = $app['validator'];

            return new RevisionRepository($model, $validator);
        });
    }
}
