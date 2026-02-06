<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * We rely on policy auto-discovery, so this is left empty.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->hasRole(RoleEnum::ADMIN->value)) {
                return true;
            }

            return null;
        });
    }
}

