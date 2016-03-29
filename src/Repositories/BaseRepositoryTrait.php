<?php

namespace Wind\Repositories;

use Dingo\Api\Exception\StoreResourceFailedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * BaseRepositoryTrait.
 */
trait BaseRepositoryTrait
{
    public function fields()
    {
        /*
         * 获取基本字段
         * @var [type]
         */
        $model = $this->model;

        return $model::$index;
    }

    /**
     * Create a new model.
     *
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $input)
    {
        $model = $this->model;

        // return $model::create($input);
        $obj = $model::create($input); //新增数据需要重新获取其他字段后返回
        return $model::find($obj->id);
    }

    /**
     * update an exist model.
     *
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $input)
    {
        $model = $this->find($id);

        return $model->update($input) ? $model->fill($input) : false;
    }

    /**
     * 当前登录用户修改资源对象，确保如果是普通用户时只能为创建者只能修改自己的记录，否则返回403 forbid.
     *
     * @param [type] $id          [description]
     * @param array  $input       [description]
     * @param [type] $user        [description]
     * @param string $owner_field [description]
     *
     * @return [type] [description]
     */
    public function updateByUser($id, array $input, $user, $owner_field = 'user_id')
    {
        $model = $this->find($id);

        if ($user->role == 'user') {
            if ($model->$owner_field != $user->id) {
                throw new AccessDeniedHttpException('无权访问资源');
            }
        }

        return $model->update($input) ? $model->fill($input) : false;
    }

    /**
     * 当前登录用户删除资源对象，确保如果是普通用户时只能为创建者只能删除自己的记录，否则返回403 forbid.
     *
     * @param [type] $id          [description]
     * @param [type] $user        [description]
     * @param string $owner_field [description]
     *
     * @return [type] [description]
     */
    public function deleteByUser($id, $user, $owner_field = 'user_id')
    {
        $model = $this->find($id);

        if ($user->role == 'user') {
            if ($model->$owner_field != $user->id) {
                throw new AccessDeniedHttpException('无权访问资源');
            }
        }

        return $model->delete() ? $model : false;
    }

    /**
     * Find an existing model.
     *
     * @param int      $id
     * @param string[] $columns
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, array $columns = ['*'])
    {
        $model = $this->model;

        $obj = $model::find($id, $columns);
        if (!$obj) {
            throw new NotFoundHttpException('资源不存在');
        }

        return $obj;
    }

    /**
     * Find all models with pagination.
     *
     * @param string[] $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($per_page = 15, array $columns = ['*'])
    {
        $model = $this->model;

        return $model::select($columns)->orderBy('id', 'desc')->paginate($per_page);
    }

    /**
     * delete model.
     *
     * @param string[] $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function delete($id)
    {
        $model = $this->find($id);

        return $model->delete() ? $model : false;
    }

    /**
     * 批量操作，返回操作成功的个数.
     *
     * @param [type] $operation [description]
     * @param array  $ids       [description]
     *
     * @return [type] [description]
     */
    public function batch($operation, array $ids)
    {
        switch ($operation) {
            case 'delete':
                $data = ['deleted_at' => time()];
                break;
            case 'forbid':
                $data = ['x_status' => 0];
                break;
            case 'startup':
                $data = ['x_status' => 1];
                break;
            default:
                break;
        }

        return $this->model->whereIn('id', $ids)->update($data);
    }

    /**
     * Register an observer.
     *
     * @param object $observer
     *
     * @return $this
     */
    public function observe($observer)
    {
        $model = $this->model;
        $model::observe($observer);

        return $this;
    }

    /**
     * 获取表单验证规则，如果指定了$query则仅返回其携带参数的对应规则，否则返回规则全集。
     *
     * @param string|string[] $query
     *
     * @return string[]
     */
    public function rules($query = null)
    {
        $model = $this->model;

        // get rules from the model if set
        if (isset($model::$rules)) {
            $rules = $model::$rules;
        } else {
            $rules = [];
        }

        // if the there are no rules
        if (!is_array($rules) || !$rules) {
            // return an empty array
            return [];
        }
        // if the query is empty
        if (!$query) {
            // return all of the rules
            return array_filter($rules);
        }

        // return the relevant rules
        return array_filter(array_only($rules, $query));
    }

    /**
     * 数据请求验证
     * 失败时直接抛出200异常.
     *
     * @param array           $data
     * @param string|string[] $rules
     * @param bool            $custom 使用自定义规则标识
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validate(array $data, $rules = null, $custom = false)
    {
        if (!$custom) {
            $rules = $this->rules($rules); //计算期望的规则子集
        }

        // 验证失败时抛出异常直接响应客户端
        $v = $this->validator->make($data, $rules);
        if ($v->fails()) {
            throw new StoreResourceFailedException('参数验证失败', $v->errors());
        }
    }
}
