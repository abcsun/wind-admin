<?php

namespace Wind\Models;

use Wind\Models\Relations\HasOneUserTrait;

/**
 * Date: 16/3/22
 * Author: eric <eric@winhu.com>.
 */
class RevisionModel extends AbstractModel
{
    use HasOneUserTrait;
    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'revisions';

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'revisions';

    /**
     *可批量操作列名.
     *
     * @var array
     */
    protected $fillable = array();

//    protected $dates = ['created_at', 'updated_at', 'disabled_at'];
//    protected $dateFormat = 'Y-m-d H:i:s';
    /**
     * The user validation rules.
     *
     * @var array
     */
//    public static $rules = [
//        'name' => 'required|unique:config,name',
//        'title' => 'required',
//        'sort' => 'numeric',
//    ];

    /**
     * The max users per page when displaying a paginated index.
     *
     * @var int
     */
    public static $paginate = 20;
}
