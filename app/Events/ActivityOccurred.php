<?php

namespace App\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityOccurred
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $action,
        public readonly ?string $description = null,
        public readonly ?Model $subject = null,
        public readonly array $before = [],
        public readonly array $after = [],
        public readonly ?Authenticatable $user = null,
    ) {}
}
