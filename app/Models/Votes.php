<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Votes extends Model
{
    use HasFactory;

    protected $table = 'votes';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'nonIncrementing';
    public $primaryKey = null;

    protected $fillable = [
        'id_option',
        'id_developer',
    ];

    public function pollOption(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'id_option');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_developer');
    }
}
