<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progress';

    protected $fillable = [
        'task_id',
        'persentase',
        'keterangan',
        'tanggal_update',
    ];

    /**
     * Get the task associated with this progress log.
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
