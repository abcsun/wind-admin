<?php

namespace Wind\Models\Relations;

/**
 * 关联roles作为user对应的角色.
 */
trait BelongsToManyRolesTrait
{
    public function roles()
    {
        return $this->belongsToMany('Wind\Models\RoleModel', 'user_role', 'user_id', 'role_id')
                ->select('role.id', 'role.name')
                ->whereNull('user_role.deleted_at');
    }
}
