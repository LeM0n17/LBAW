<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Events extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the user that owns the event.
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_host');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'id_event');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notifications::class, 'id_event');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'id_event');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(TagConnection::class, 'id_event');
    }
    
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'id_event');
    }

    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class, 'id_event');
    }

    public function hasPendingRequest($userId)
    {
        return $this->notifications()
            ->where('id_developer', $userId)
            ->where('event_notification_type', 'request')
            ->exists();
    }

    public function hasBeenInvited($userId)
    {
        return $this->notifications()
            ->where('id_developer', $userId)
            ->where('event_notification_type', 'invite')
            ->exists();
    }
}