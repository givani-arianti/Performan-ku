<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklist extends Model
{
    protected $fillable = [
        'task_id',
        'judul',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    /**
     * Get the task that owns this checklist item.
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
