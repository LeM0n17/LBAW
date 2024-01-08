<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Events;
use App\Models\Admin;

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
    public function show(User $user, Events $event): bool
    {
        // Any (authenticated) user can see public events.
        return Auth::check();
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
     * Determine if an event can be created by a user.
     */
    public function create(User $user): bool
    {
        // Any user can create a new event.
        return Auth::check();
    }

    /**
     * Determine if an event can be deleted by a user.
     */
    public function delete(User $user, Events $event): bool
    {
      // Only a host can delete it.
      return $user->id === $event->id_host || $user->isAdmin();
    }

    public function editEvents(User $user, Events $event): bool
    {
        return $user->id === $event->id_host || $user->isAdmin();
    }

}
