<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\CartItems;

class ShoppingCart extends Model
{
    protected $fillable = ['user_id'];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItems::class);
    }

    public function getTotalAttribute()
    {
        return $this->cartItems->sum(fn($item) => $item->quantity * $item->price);
    }
}
