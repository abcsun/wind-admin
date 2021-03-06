<?php

namespace Wind\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        'Wind\Events\UserRoleRelationChangedEvent' => [
            'Wind\Listeners\UserRoleRelationChangedListener',
        ],
    ];
}
