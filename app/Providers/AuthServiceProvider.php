<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Passport::ignoreRoutes();

        /* define a admin user role */
        Gate::define('isAdmin', function($user) {
            return $user->roles->contains('name', 'admin');
        });

        /* define a manager user role */
        Gate::define('isManager', function($user) {
            return $user->roles->contains('name', 'manager');
        });

        /* define a user role */
        Gate::define('isUser', function($user) {
            return $user->roles->contains('name', 'user');
        });
    }
}
