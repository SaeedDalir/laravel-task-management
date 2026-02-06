<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

trait TaskFilter
{
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(isset($filters['status']), function (Builder $q) use ($filters) {
                $statuses = (array) $filters['status'];

                $q->whereIn('status', $statuses);
            })
            ->when(isset($filters['owner_id']), function (Builder $q) use ($filters) {
                $q->where('user_id', (int) $filters['owner_id']);
            })
            ->when(isset($filters['assigned_user_id']), function (Builder $q) use ($filters) {
                $assignedUserId = (int) $filters['assigned_user_id'];

                $q->whereHas('users', function (Builder $q2) use ($assignedUserId) {
                    $q2->where('user_id', $assignedUserId);
                });
            })
            ->when(isset($filters['due_date_from']), function (Builder $q) use ($filters) {
                $q->whereDate('due_date', '>=', $filters['due_date_from']);
            })
            ->when(isset($filters['due_date_to']), function (Builder $q) use ($filters) {
                $q->whereDate('due_date', '<=', $filters['due_date_to']);
            })
            ->when(isset($filters['created_from']), function (Builder $q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['created_from']);
            })
            ->when(isset($filters['created_to']), function (Builder $q) use ($filters) {
                $q->whereDate('created_at', '<=', $filters['created_to']);
            })
            ->when(isset($filters['search']), function (Builder $q) use ($filters) {
                $search = trim((string) $filters['search']);

                if ($search === '') {
                    return;
                }

                $q->where(function (Builder $q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });
    }
}

