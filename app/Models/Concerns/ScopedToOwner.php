<?php

namespace App\Models\Concerns;

use App\Models\User;

/**
 * Adds an ownership query scope: sales reps only see their own records,
 * while admins and managers see everything.
 */
trait ScopedToOwner
{
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->canManageAll()) {
            return $query;
        }

        return $query->where('owner_id', $user->id);
    }
}
