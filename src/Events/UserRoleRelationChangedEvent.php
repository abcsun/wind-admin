<?php

namespace Wind\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

// use App\Models\UserModel;

class UserRoleRelationChangedEvent extends Event
{
    // use SerializesModels;

    public $user_id;

    /**
     * Create a new event instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
