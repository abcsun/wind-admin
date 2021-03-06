<?php

namespace Wind\Http\Controllers;

use Cache;
use Config;
use Illuminate\Http\Request;
use JWTAuth;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dingo\Api\Routing\Helpers;
use Wind\Transformers\Serializers\JsonApiSerializer;

date_default_timezone_set('PRC'); //设置本地时区

/**
 * 基类控制器.
 *
 * @author scl <scl@winhu.com>
 */
class BaseController extends Controller
{
    use Helpers;

    //用户信息
    protected $user;

    // repostory
    protected $r;

    public function __construct(Request $request)
    {
        // $this->user = $this->checkUserByToken();
        $this->user = $request->user();
    }

    /**
     * index路由，用于资源分页展示.
     * 
     * @return [type] [description]
     */
    public function index(Request $request)
    {
        $per_page = (int) $request->input('per_page', 12);
        $data = $this->r->all($per_page);

        return response_json(1, $data, '获取成功', 200);
    }

    /**
     * 创建新的记录.
     * 
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $this->r->validate($data);

        $item = $this->r->create($data);

        return response_json(1, $item, '创建成功');
    }

    /**
     * 修改已有资源.
     *
     * @param Request $request [description]
     * @param [type]  $id      [description]
     *
     * @return [type] [description]
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $item = $this->r->update($id, $data);

        return response_json(1, $item, '修改成功');
    }

    /**
     * 获取单个资源.
     *
     * @param Request $request [description]
     * @param [type]  $id      [description]
     *
     * @return [type] [description]
     */
    public function show(Request $request, $id)
    {
        $item = $this->r->find($id);

        return response_json(1, $item, '获取成功');
    }

    /**
     * 删除单个资源.
     *
     * @param Request $request [description]
     * @param [type]  $id      [description]
     *
     * @return [type] [description]
     */
    public function delete(Request $request, $id)
    {
        $item = $this->r->delete($id);

        return response_json(1, '删除成功', '删除成功');
    }

    /**
     * 批量修改已有资源接口
     * 请求为json格式，如下
     * {
     *     "data": [
     *         {"id":8, "data":{}},
     *         {"id":9, "data":{}}
     *        ]
     *   }.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function batchUpdate(Request $request)
    {
        $data = $request->input('data', []);
        $success_ids = [];
        $success_count = 0;
        foreach ($data as $key => $value) {
            try {
                if ($this->r->update($value['id'], $value['data'])) {
                    $success_ids[] = $value['id'];
                    ++$success_count;
                }
            } catch (NotFoundHttpException $e) {
            }
        }

        return response_json(1, $success_ids, '成功修改'.$success_count.'个记录');
    }

    /**
     * 批量软删除.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function batchSoftDelete(Request $request)
    {
        $str = $request->input('ids', '');
        $ids = parse_ids_from_str($str);
        $count = $this->r->getModel()->whereIn('id', $ids)->delete();

        return response_json(1, $count, '成功删除'.$count.'个记录');
    }

    /**
     * 用户批量操作.
     *
     * @param Request $request   [description]
     * @param [type]  $operation [description]
     *
     * @return [type] [description]
     */
    public function batchOperation(Request $request, $operation)
    {
        $operation_types = [
            'delete',
            'enable',
            'disable',
        ];
        if (!in_array($operation, $operation_types)) {
            return response_json(0, '', '请求不存在', 404);
        }

        $str = $request->input('ids', '');
        $ids = parse_ids_from_str($str);
        $result = $this->r->batch($operation, $ids);

        return response_json(1, $result, '批量操作成功');
    }

    /**
     * 基于Dingo的transformer对单个数据进行统一，并采用自定义的JsonApiSerializer进行格式化.
     *
     * @param [type] $model       [description]
     * @param [type] $transformer [description]
     * @param int    $code        [description]
     * @param string $msg         [description]
     * @param int    $http_status [description]
     * @param string $type        [description]
     *
     * @return [type] [description]
     */
    public function item($model, $transformer, $code = 1, $msg = 'msg', $http_status = 200, $type = '')
    {
        return $this->response()->item(
                $model,
                $transformer,
                ['key' => $type],
                function ($resource, $fractal) use ($code, $msg) {
                    $fractal->setSerializer(new JsonApiSerializer($code, $msg));
                }
            )->statusCode($http_status);
    }

    /**
     * 基于Dingo的transformer对结果集进行统一，并采用自定义的JsonApiSerializer进行格式化.
     *
     * @param [type] $model       [description]
     * @param [type] $transformer [description]
     * @param int    $code        [description]
     * @param string $msg         [description]
     * @param int    $http_status [description]
     * @param string $type        [description]
     *
     * @return [type] [description]
     */
    public function collection($models, $transformer, $code = 1, $msg = 'msg', $http_status = 200, $type = '')
    {
        return $this->response()->collection(
                $models,
                $transformer,
                ['key' => $type],
                function ($resource, $fractal) use ($code, $msg) {
                    $fractal->setSerializer(new JsonApiSerializer($code, $msg));
                }
            )->statusCode($http_status);
    }

    /**
     * 基于Dingo的transformer对分页数据进行统一，并采用自定义的JsonApiSerializer进行格式化.
     *
     * @param [type] $models      [description]
     * @param [type] $transformer [description]
     * @param int    $code        [description]
     * @param string $msg         [description]
     * @param int    $http_status [description]
     * @param string $type        [description]
     *
     * @return [type] [description]
     */
    public function paginate($models, $transformer, $code = 1, $msg = 'msg', $http_status = 200, $type = '')
    {
        return $this->response()->paginator(
                $models,
                $transformer,
                ['key' => $type],
                function ($resource, $fractal) use ($code, $msg) {
                    $fractal->setSerializer(new JsonApiSerializer($code, $msg));
                }
            )->statusCode($http_status);
    }

    /**
     * 根据请求中的token从缓存中直接获取用户.
     *
     * @return UserModel
     */
    public function checkUserByToken()
    {
        return Cache::get(Config('sys.USER_CACHE_PREFIX').md5(JWTAuth::getToken()));
    }

    /**
     * 检查资源是否存在，不存在时抛出404异常，终止运行.
     * 
     * @param $model
     * @param $msg
     */
    public function checkModelExists($model, $msg = '资源不存在')
    {
        if (!$model) {
            throw new NotFoundHttpException($msg);
        }
    }
}
