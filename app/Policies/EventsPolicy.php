<?php

namespace App\Policies;

use App\Models\UserJammer;
use App\Models\Users;
use App\Models\Events;

use Illuminate\Support\Facades\Auth;

class EventsPolicy
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
    public function show(UserJammer $user, Events $event): bool
    {
        // Only a card owner can see a card.
        return $user->id === $event->id_host;
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
    public function create(UserJammer $user): bool
    {
        // Any user can create a new card.
        return Auth::check();
    }

    /**
     * Determine if a card can be deleted by a user.
     */
    public function delete(UserJammer $user, Events $event): bool
    {
      // Only a card owner can delete it.
      return $user->id === $event->id_host;
    }
}
