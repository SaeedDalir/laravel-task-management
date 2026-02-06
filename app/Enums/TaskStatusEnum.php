<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    use BaseEnum;

    case PENDING = 'pending';
    case COMPLETED = 'completed';
}


