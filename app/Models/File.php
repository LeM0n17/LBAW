<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use HasFactory;

    protected $table = 'file';
    protected $guarded = [];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'id_developer');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'id_event');
    }

    // public function likes(): HasMany
    // {
    //     return $this->hasMany(Like::class, 'id_comment');
    // }

    // public function isLikedBy(User $user)
    // {
    //     return $this->likes->where('likes', true)->contains('id_developer', $user->id);
    // }

    // public function isDislikedBy(User $user)
    // {
    //     return $this->likes->where('likes', false)->contains('id_developer', $user->id);;
    // }

    // public function likesCount(): int
    // {
    //     return $this->likes()->where('likes', true)->count();
    // }

    // public function dislikesCount(): int
    // {
    //     return $this->likes()->where('likes', false)->count();
    // }
}
