<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * In this case it creates a profile when the user is created
     */
    public function created(User $user): void
    {
        $user->profile()->create();
    }
}
