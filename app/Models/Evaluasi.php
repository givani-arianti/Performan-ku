<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    protected $table = 'evaluasi';

    protected $fillable = [
        'task_id',
        'id_manajer',
        'status_evaluasi',
        'catatan',
        'score',
        'kualitas',
        'komunikasi',
        'kerja_sama',
        'disiplin_tanggung_jawab',
        'problem_solving',
        'tanggal_evaluasi',
    ];

    protected $casts = [
        'kualitas' => 'integer',
        'komunikasi' => 'integer',
        'kerja_sama' => 'integer',
        'disiplin_tanggung_jawab' => 'integer',
        'problem_solving' => 'integer',
    ];

    /**
     * Get the task associated with this evaluation.
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Get the manager who evaluated this task.
     */
    public function manajer()
    {
        return $this->belongsTo(User::class, 'id_manajer');
    }
}
