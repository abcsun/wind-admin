<?php

namespace Wind\Models\Relations;

/**
 * 关联user_profile作为主体信息的profile.
 */
trait HasOneProfileTrait
{
    public function profile()
    {
        return $this->hasOne('Wind\Models\UserProfileModel', 'user_id', 'id')->select('*');
    }
}
