<?php

namespace Wind\Listeners;

use Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Wind\Events\UserRoleRelationChangedEvent;
use Wind\Models\UserRoleModel;

class UserRoleRelationChangedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * 用户角色关系发生变动时，及时更新user所具有的当前身份.
     *
     * @param SomeEvent $event
     */
    public function handle(UserRoleRelationChangedEvent $event)
    {
        // var_dump('UserRoleRelationChangedListener');
        $user_id = $event->user->id;
        $res = UserRoleModel::leftJoin('role', function ($join) {
                        $join->on('user_role.role_id', '=', 'role.id')
                            ->whereNull('role.deleted_at');
                    })
                    ->where('user_role.user_id', $user_id)
                    ->whereNotNull('role.type')
                    ->distinct()
                    ->lists('role.type')
                    ->toArray();

        $roles = array(
            '0' => 'unknow',
            '1' => 'admin',
            '2' => 'user',
        );
        // role的能力按照与type逆向的方式表示，及type越小能力越高
        sort($res, SORT_NUMERIC);
        if (isset($res[0]) && $res[0] === 1) {
            $event->user->role = 'admin';
        } else {
            $event->user->role = 'user';
        }

        $event->user->save();
    }
}
