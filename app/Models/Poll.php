<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasFactory;

    protected $table = 'poll';
    protected $fillable = [
        'id_event',
        'title',
    ];
    public $timestamps = false;

    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'id_event');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class, 'id_poll');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Votes::class, 'id_poll');
    }

    public function getTotalVotes()
    {
        return $this->options->sum(function ($option) {
            return $option->votes->count();
        });
    }
}
