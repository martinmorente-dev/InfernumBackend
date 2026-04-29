<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Genre;
use App\Models\ImageGame;
use App\Models\Discount;
use App\Models\CartItems;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    protected $fillable = [
        'name',
        'short_description',
        'long_description',
        'price',
        'discount_id'
    ];


    public $timestamps = false;


    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'games_genres');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ImageGame::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }

    public function discounts(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discounts_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItems::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'libraries', 'game_id', 'user_id');
    }

    public function portraitImage(): HasOne
    {
        return $this->hasOne(ImageGame::class)->where('type', 'portrait');
    }

    public function getActiveDiscounts()
    {
        return $this->discounts()->active()->first();
    }

    public function scopeGameByGenre($query, string $genreName)
    {
        return $query->whereHas('genres', function ($q) use ($genreName) {
            $q->where('type', $genreName);
        });
    }
}
