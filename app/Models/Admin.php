<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasFactory;

    public $timestamp = false;

    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }
}