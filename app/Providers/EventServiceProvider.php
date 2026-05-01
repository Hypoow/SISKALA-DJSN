<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\ActivityDocumentation;
use App\Models\ActivityFollowup;
use App\Models\ActivityMaterial;
use App\Models\ActivityMom;
use App\Models\Division;
use App\Models\Position;
use App\Models\Staff;
use App\Models\User;
use App\Observers\ActivityFollowupObserver;
use App\Observers\ActivityObserver;
use App\Observers\ActivityRelationObserver;
use App\Observers\DivisionObserver;
use App\Observers\PositionObserver;
use App\Observers\StaffObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Activity::observe(ActivityObserver::class);
        ActivityFollowup::observe(ActivityFollowupObserver::class);
        ActivityMaterial::observe(ActivityRelationObserver::class);
        ActivityMom::observe(ActivityRelationObserver::class);
        ActivityDocumentation::observe(ActivityRelationObserver::class);
        User::observe(UserObserver::class);
        Division::observe(DivisionObserver::class);
        Position::observe(PositionObserver::class);
        Staff::observe(StaffObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
