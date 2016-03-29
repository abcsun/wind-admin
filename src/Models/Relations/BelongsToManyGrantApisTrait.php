<?php

namespace Wind\Models\Relations;

/**
 * 关联grant_apis作为menu对应的授权api.
 */
trait BelongsToManyGrantApisTrait
{
    public function grant_apis()
    {
        return $this->belongsToMany('Wind\Models\GrantApiModel', 'permission_grant_api', 'permission_id', 'grant_api_id')
                ->select('grant_api.id', 'grant_api.name', 'grant_api.slug')
                ->whereNull('permission_grant_api.deleted_at');
    }
}
