<?php

namespace Wind\Listeners;

use Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Wind\Events\UserRoleRelationChangedEvent;

class UserRoleRelationChangedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param SomeEvent $event
     */
    public function handle(UserRoleRelationChangedEvent $event)
    {
        // var_dump('UserRoleRelationChangedListener');
    }
}
