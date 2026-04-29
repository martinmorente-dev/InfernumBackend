<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Profile;
use App\Models\ShoppingCart;
use App\Models\Game;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Billable;

    protected $table = 'users';

    const UPDATED_AT = null;

    protected $fillable = [
        'nickname',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'create_at'
    ];

    protected $casts = [
        'password' => 'hashed'
    ];

    public function shoppingCart(): HasOne
    {
        return $this->hasOne(ShoppingCart::class);
    }

    public function Profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'libraries', 'user_id', 'game_id');
    }
}
