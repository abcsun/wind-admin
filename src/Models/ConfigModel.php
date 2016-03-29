<?php
/**
 * Created by PhpStorm.
 * Date: 16/3/12
 * Author: eric <eric@winhu.com>.
 */

namespace Wind\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigModel extends AbstractModel
{
    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'config';

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'grant_api';
    
    /**
     *可批量操作列名.
     *
     * @var array
     */
    protected $fillable = array('name', 'title', 'group', 'value', 'x_status', 'type', 'remark', 'sort');

    /**
     * The user validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:config,name',
        'title' => 'required',
        'sort' => 'numeric',
    ];

    /**
     * The max users per page when displaying a paginated index.
     *
     * @var int
     */
    public static $paginate = 20;

    /**
     * name值转为大写.
     *
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    /**
     * 根据配置类型type解析出正确的值value.
     *
     * @param [type] $value [description]
     *
     * @return [type] [description]
     */
    public function getValueAttribute($value)
    {
        return parse_config_value($this->attributes['type'], $value);
    }
}
