<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        \Gate::define('admin-full', function ($user) {
            return $user->canAccessAdminArea();
        });

        \Gate::define('manage-activities', function ($user) {
            return $user->canManageActivities();
        });

        \Gate::define('manage-minutes', function ($user) {
            return $user->canManagePostActivity();
        });
        
        \Gate::define('manage-attendance', function ($user) {
            return $user->canManagePostActivity();
        });

        \Gate::define('manage-followups', function ($user) {
            return $user->canManageFollowUp();
        });

        \Gate::define('manage-documentation', function ($user) {
            return $user->canManageDocumentation();
        });

        \Gate::define('manage-topics', function ($user) {
            return $user->canManageTopics();
        });
    }
}
