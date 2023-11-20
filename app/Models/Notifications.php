<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifications extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_developer',
        'id_event',
        'type',
        'content',
        'time'
    ];
    public $timestamps = false;

    /**
     * Get the user that owns the event.
     */
    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_developer');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'id_event');
    }
}
