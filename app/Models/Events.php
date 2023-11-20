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
}
