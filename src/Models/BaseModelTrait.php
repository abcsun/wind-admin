<?php

namespace Wind\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/**
 * This is the base model trait.
 */
trait BaseModelTrait
{
    /**
     * Create a new model.
     *
     * @param array $input
     *
     * @throws \Exception
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function create(array $input = [])
    {
        DB::beginTransaction();

        try {
            Event::fire(static::$name.'.creating');
            static::beforeCreate($input);
            $return = parent::create($input);
            static::afterCreate($input, $return);
            Event::fire(static::$name.'.created');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $return;
    }

    /**
     * Before creating a new model.
     *
     * @param array $input
     */
    public static function beforeCreate(array $input)
    {
        // can be overwritten by extending class
    }

    /**
     * After creating a new model.
     *
     * @param array                               $input
     * @param \Illuminate\Database\Eloquent\Model $return
     */
    public static function afterCreate(array $input, Model $return)
    {
        // can be overwritten by extending class
    }

    /**
     * Update an existing model.
     *
     * @param array $input
     *
     * @throws \Exception
     *
     * @return bool|int
     */
    public function update(array $input = [], array $options = []) //5.2省略$options时有兼容性异常
    {
        DB::beginTransaction();

        try {
            Event::fire(static::$name.'.updating', $this);
            $this->beforeUpdate($input);
            $return = parent::update($input);
            $this->afterUpdate($input, $return);
            Event::fire(static::$name.'.updated', $this);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $return;
    }

    /**
     * Before updating an existing new model.
     *
     * @param array $input
     */
    public function beforeUpdate(array $input)
    {
        // can be overwritten by extending class
    }

    /**
     * After updating an existing model.
     *
     * @param array    $input
     * @param bool|int $return
     */
    public function afterUpdate(array $input, $return)
    {
        // can be overwritten by extending class
    }

    /**
     * Delete an existing model.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function delete()
    {
        DB::beginTransaction();

        try {
            Event::fire(static::$name.'.deleting', $this);
            $this->beforeDelete();
            $return = parent::delete();
            $this->afterDelete($return);
            Event::fire(static::$name.'.deleted', $this);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $return;
    }

    /**
     * Before deleting an existing model.
     */
    public function beforeDelete()
    {
        // can be overwritten by extending class
    }

    /**
     * After deleting an existing model.
     *
     * @param bool $return
     */
    public function afterDelete($return)
    {
        // can be overwritten by extending class
    }

    // 由于使用transformer中直接读取model中的时间会自动解析为date类型结果，因此需要model直接将时间返回string类型
    public function getUpdatedAtAttribute($value)
    {
        return $value;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value;
    }
}
