<?php

namespace App\Enums;

enum PermissionEnum: string
{
    use BaseEnum;

    case VIEW_TASKS = 'view_tasks';
    case CREATE_TASK = 'create_task';
    case UPDATE_TASK = 'update_task';
    case DELETE_TASK = 'delete_task';
    case MANAGE_USERS = 'manage_users';
}
