<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        auth()->shouldUse('user');
    }
}