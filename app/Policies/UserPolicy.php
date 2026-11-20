<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the manager can view an employee's performance detail.
     */
    public function viewPerformance(User $user, User $employee): bool
    {
        if ($user->role !== 'manajer' || $employee->role !== 'karyawan') {
            return false;
        }

        if (! $user->divisi) {
            return true;
        }

        return $employee->divisi === $user->divisi;
    }
}
