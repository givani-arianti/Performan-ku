<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the manager can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        return $this->managesTask($user, $task);
    }

    /**
     * Determine whether the manager can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        return $this->managesTask($user, $task);
    }

    /**
     * Determine whether the manager can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $this->managesTask($user, $task);
    }

    /**
     * Determine whether the manager can evaluate the task.
     */
    public function evaluate(User $user, Task $task): bool
    {
        return $this->managesTask($user, $task);
    }

    /**
     * A manager may only manage tasks from their own division.
     * Managers without a division keep the existing global access.
     */
    private function managesTask(User $user, Task $task): bool
    {
        if ($user->role !== 'manajer') {
            return false;
        }

        if (! $user->divisi) {
            return true;
        }

        return $task->divisi === $user->divisi
            || $task->karyawan?->divisi === $user->divisi;
    }
}
