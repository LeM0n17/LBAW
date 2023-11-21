<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Participant extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
    public $incrementing = false;
    protected $keyType = 'nonIncrementing';
    public $primaryKey = null;

    protected $fillable = [
        'id_participant',
        'id_event',
    ];

    /**
     * Get the user.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_participant');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'id_event');
    }

}
