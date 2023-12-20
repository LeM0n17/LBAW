<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class PollOption extends Model
{
    use HasFactory;

    protected $table = 'option';

    public $timestamps = false;

    protected $fillable = [
        'id_poll',
        'name',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class, 'id_poll');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Votes::class, 'id_option');
    }

    public function countVotes(): int
    {
        return $this->votes()->count();
    }

    public function getVotePercentage()
    {
        $totalVotes = $this->poll->getTotalVotes();

        if ($totalVotes == 0) {
            return 999999;
        }

        return ($this->votes->count() / $totalVotes) * 100;
    }

    public function hasVoted(): bool
    {
        return $this->votes()->where('id_developer', Auth::user()->id)->exists();
    }
}
