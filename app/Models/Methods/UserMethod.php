<?php

namespace App\Models\Methods;

use App\Enums\RoleEnum;

trait UserMethod
{
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleEnum::ADMIN->value);
    }

    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return in_array($this->role?->value, $role, true);
        }

        return $this->role?->value === $role;
    }

    public function hasPermission(string|array $permission): bool
    {
        $role = $this->role?->value;
        if (! $role) {
            return false;
        }

        $rolePermissions = config("permissions.{$role}", []);
        if (in_array('*', $rolePermissions, true)) {
            return true;
        }

        $check = is_array($permission) ? $permission : [$permission];
        foreach ($check as $p) {
            if (in_array($p, $rolePermissions, true)) {
                return true;
            }
        }

        return false;
    }
}