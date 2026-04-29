<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Profile extends Model
{
    protected $fillable = [
        'display_name',
        'bio',
        'profile_picture'
    ];

    public $timestamps = false;

    public function Users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
