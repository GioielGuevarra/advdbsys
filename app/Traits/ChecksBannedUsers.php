<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait ChecksBannedUsers
{
    protected function checkIfBanned()
    {
        if (Auth::user()->account->is_banned) {
            abort(403, 'Your account has been banned. You cannot perform any transactions at this time. Please contact support.');
        }
    }
}