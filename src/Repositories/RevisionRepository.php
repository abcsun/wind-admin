<?php

namespace Wind\Repositories;

/*
 * Date: 16/3/22
 * Author: eric <eric@winhu.com>.
 */

class RevisionRepository extends AbstractRepository
{
    /**
     * @param $per_page
     *
     * @return mixed
     */
    public function dataList($per_page)
    {
        $model = $this->model;

        $list = $model::leftJoin('user', 'revisions.user_id', '=', 'user.id')
                ->select('revisions.id', 'revisions.revisionable_type', 'revisions.key', 'revisions.old_value', 'revisions.revisionable_id', 'revisions.new_value', 'revisions.created_at', 'revisions.updated_at', 'user.name', 'user.phone')
                ->paginate($per_page);

        return $list;
    }

    /**
     *删除数据.
     *
     * @param $ids
     *
     * @return bool
     */
    public function deleteRevisions($ids)
    {
        $model = $this->model;
        $ids = parse_ids_from_str($ids);

        $return = $model::whereIn('id', $ids)
                 ->delete();

        if ($return) {
            return $return;
        }

        return false;
    }

    /**
     * 清空数据.
     *
     * @return bool|\Illuminate\Database\Eloquent\Model
     */
    public function clearTableData()
    {
        $model = $this->model;
        if ($model->truncate()) {
            return $model;
        }

        return false;
    }
}
