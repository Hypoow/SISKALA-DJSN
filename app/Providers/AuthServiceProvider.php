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

        // Level 0 & 1: Full Access (Admin, DJSN)
        \Gate::define('admin-full', function ($user) {
            return $user->getAdminLevel() <= 1;
        });

        // Level 2: Tata Usaha + Above (Manage Activities, Upload Surat Tugas)
        \Gate::define('manage-activities', function ($user) {
            return $user->getAdminLevel() <= 2;
        });

        // Level 3: Persidangan + Above (Minutes, Attendance, Follow-up, Materials)
        \Gate::define('manage-minutes', function ($user) {
            return $user->getAdminLevel() <= 3;
        });
        
        \Gate::define('manage-attendance', function ($user) {
            return $user->getAdminLevel() <= 3;
        });

        \Gate::define('manage-followups', function ($user) {
            return $user->getAdminLevel() <= 3;
        });

        // Level 4: Bagian Umum + Above (Documentation)
        \Gate::define('manage-documentation', function ($user) {
            return $user->getAdminLevel() <= 4;
        });
    }
}
