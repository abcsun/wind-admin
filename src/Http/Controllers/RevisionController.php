<?php

namespace Wind\Http\Controllers;

use Illuminate\Http\Request;
use Wind\Facades\RevisionRepository as RevisionRepo;
use Wind\Transformers\RevisionTransformer;

/**
 *y用户操作历史纪录
 * Date: 16/3/22
 * Author: eric <eric@winhu.com>.
 */
class RevisionController extends BaseController
{
    /**
     * 返回历史纪录.
     *
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = (int) $request->input('per_page', 12);
        $list = RevisionRepo::dataList($per_page);

        return $this->paginate($list, new RevisionTransformer(), 1, '获取成功');
    }

    /**
     * 删除历史纪录.
     *
     * @param $id
     *
     * @return $this
     */
    public function delete(Request $request)
    {
        $ids = $request->input('ids');
        $list = RevisionRepo::deleteRevisions($ids);

        if ($list) {
            return response_json(1, $list, '操作成功');
        }

        return response_json(0, $list, '操作失败');
    }

    /**
     * 清空数据.
     *
     * @return $this
     */
    public function clear()
    {
        $return = RevisionRepo::clearTableData();

        if ($return) {
            return response_json(1, $return, '操作成功');
        }

        return response_json(0, $return, '操作失败');
    }
}
