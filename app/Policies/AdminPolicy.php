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
        return $user->isAdmin();
    }

    public function deleteUser(): bool
    {
        return Auth::user()->isAdmin();
    }

    public function deleteEvent(User $user): bool
    {
        return $user->isAdmin();
    }

    public function deleteTag(User $user): bool
    {
        return $user->isAdmin();
    }

    public function createTag(User $user): bool
    {
        return $user->isAdmin();
    }

}
