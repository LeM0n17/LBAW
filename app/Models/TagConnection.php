<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagConnection extends Model
{
    use HasFactory;
    protected $table = 'event_tag';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
    public $incrementing = false;
    protected $keyType = 'nonIncrementing';
    public $primaryKey = null;

    protected $fillable = [
        "id_event",
        "id_tag"
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'id_tag');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'id_event');
    }
}