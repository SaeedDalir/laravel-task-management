<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait TaskVisibleForScope
{
    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhereHas('users', function (Builder $q2) use ($user) {
                    $q2->where('user_id', $user->id);
                });
        });
    }
}

