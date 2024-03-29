<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Events;
use App\Models\Notifications;

use Illuminate\Support\Facades\Auth;

class NotificationsPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if a given card can be shown to a user.
     */
    public function show(User $user, Notifications $notification): bool
    {
        // Only a card owner can see a card.
        return $user->id === $notification->id_developer;
    }

    /**
     * Determine if all events can be listed by a user.
     */
    public function list(): bool
    {
        // Any (authenticated) user can list its own events.
        return Auth::check();
    }

    /**
     * Determine if a card can be created by a user.
     */
    public function create(User $user): bool
    {
        // Any user can create a new card.
        return Auth::check();
    }

    /**
     * Determine if a notification can be deleted by a user.
     */
    public function delete(User $user, Notifications $notification): bool
    {
      // Only a notification owner can delete it.
      return $user->id === $notification->id_developer;
    }
}
