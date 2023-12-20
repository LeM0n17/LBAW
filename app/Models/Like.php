<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $guarded = [];

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'nonIncrementing';
    public $primaryKey = null;

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'id_comment');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_developer');
    }
}
