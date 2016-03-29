<?php

namespace Wind\Models\Relations;

/**
 * 关联user_profile作为主体信息的profile.
 */
trait HasOneUserTrait
{
    public function user()
    {
        return $this->hasOne('Wind\Models\UserModel', 'id', 'user_id')->select('name', 'phone');
    }
}
