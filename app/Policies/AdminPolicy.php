<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Events;
use App\Models\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminPolicy
{
    public function showAdminPage(User $user): bool
    {
        Log::info('AdminPolicy::$user->isAdmin(): ' . var_export($user->isAdmin(), true));
        return $user->isAdmin();
    }

    public function deleteUser(User $user, User $userToDelete): bool
    {
        return $user->isAdmin() && !$userToDelete->isAdmin();
    }

    public function deleteEvent(User $user, Events $event): bool
    {
        return $user->isAdmin();
    }

}
