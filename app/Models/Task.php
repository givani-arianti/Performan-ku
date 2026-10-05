<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'judul_tugas',
        'deskripsi',
        'target',
        'divisi',
        'deadline',
        'id_hr',
        'id_karyawan',
        'status',
        'hasil_kerja',
        'dokumen_path',
    ];

    /**
     * Get the HR who created this task.
     */
    public function hr()
    {
        return $this->belongsTo(User::class, 'id_hr');
    }

    /**
     * Get the Employee assigned to this task.
     */
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'id_karyawan');
    }

    /**
     * Get the checklist items for this task.
     */
    public function checklists()
    {
        return $this->hasMany(TaskChecklist::class, 'task_id');
    }

    /**
     * Get the progress logs for this task.
     */
    public function progress()
    {
        return $this->hasMany(Progress::class, 'task_id');
    }

    /**
     * Get the evaluation for this task (if any).
     */
    public function evaluasi()
    {
        return $this->hasOne(Evaluasi::class, 'task_id');
    }

    /**
     * Accessor to get current progress percentage.
     */
    public function getPersentaseAttribute()
    {
        $totalChecklists = $this->checklists()->count();
        if ($totalChecklists > 0) {
            $completedCount = $this->checklists()->where('is_completed', true)->count();
            return (int) round(($completedCount / $totalChecklists) * 100);
        }

        if (in_array($this->status, ['Completed', 'Reviewed'])) {
            return 100;
        }

        $latest = $this->progress()->orderBy('tanggal_update', 'desc')->first();
        return $latest ? $latest->persentase : 0;
    }

    /**
     * Method to calculate auto performance score based on progress and deadline.
     */
    public function calculateScore()
    {
        // If evaluated, use manager's score
        if ($this->evaluasi) {
            return $this->evaluasi->kualitas ?? $this->evaluasi->score;
        }

        $progress = $this->persentase;
        $deadline = \Carbon\Carbon::parse($this->deadline);

        if ($progress >= 100) {
            // Find when it reached 100%
            $completionLog = $this->progress()->where('persentase', '>=', 100)->orderBy('tanggal_update', 'asc')->first();
            $completionDate = $completionLog ? \Carbon\Carbon::parse($completionLog->tanggal_update) : \Carbon\Carbon::parse($this->updated_at);

            if ($completionDate->startOfDay()->lte($deadline->startOfDay())) {
                return 100;
            } else {
                return 70; // Penalty for late completion
            }
        }

        // Still in progress
        $now = \Carbon\Carbon::now();
        if ($now->startOfDay()->lte($deadline->startOfDay())) {
            return $progress;
        } else {
            return round($progress * 0.7); // Penalty for overdue
        }
    }
}
