<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use App\Models\Filters\TaskFilter;
use App\Models\Scopes\TaskVisibleForScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes, TaskVisibleForScope, TaskFilter;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'completed_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatusEnum::class,
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users');
    }
}

