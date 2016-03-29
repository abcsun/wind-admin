<?php

namespace Wind\Models\Relations;

/**
 * 关联permissions作为role对应的权限.
 */
trait BelongsToManyPermissionsTrait
{
    public function permissions()
    {
        return $this->belongsToMany('Wind\Models\PermissionModel', 'role_permission', 'role_id', 'permission_id')
                ->select('permission.id', 'permission.name')
                ->whereNull('role_permission.deleted_at');
    }
}
