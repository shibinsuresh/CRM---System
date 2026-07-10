<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // A user may manage a record if they own it, or if they can manage all
        // records (admins and managers). Used across all CRM controllers.
        Gate::define('manage-record', function ($user, $record) {
            return $user->canManageAll() || $record->owner_id === $user->id;
        });

        // Only admins may manage users.
        Gate::define('manage-users', function ($user) {
            return $user->isAdmin();
        });
    }
}
