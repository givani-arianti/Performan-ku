<?php

namespace App\Models;

use Carbon\Carbon;
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

    private ?int $cachedPersentase = null;

    private ?array $cachedKecepatanInfo = null;

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
     * Uses eager-loaded relations when available to avoid N+1 queries.
     */
    public function getPersentaseAttribute(): int
    {
        if ($this->cachedPersentase !== null) {
            return $this->cachedPersentase;
        }

        if ($this->relationLoaded('checklists')) {
            $totalChecklists = $this->checklists->count();
            $completedCount = $this->checklists->where('is_completed', true)->count();
        } else {
            $totalChecklists = $this->checklists()->count();
            $completedCount = $totalChecklists > 0
                ? $this->checklists()->where('is_completed', true)->count()
                : 0;
        }

        if ($totalChecklists > 0) {
            return $this->cachedPersentase = (int) round(($completedCount / $totalChecklists) * 100);
        }

        if (in_array($this->status, ['Completed', 'Reviewed'])) {
            return $this->cachedPersentase = 100;
        }

        $latest = $this->latestProgressLog();

        return $this->cachedPersentase = $latest ? (int) $latest->persentase : 0;
    }

    /**
     * Accessor to get task speed and timeliness info relative to deadline.
     */
    public function getKecepatanInfoAttribute(): array
    {
        if ($this->cachedKecepatanInfo !== null) {
            return $this->cachedKecepatanInfo;
        }

        $deadline = Carbon::parse($this->deadline)->startOfDay();
        $progress = $this->persentase;

        if ($this->status === 'Revisi') {
            $diffInDays = (int) now()->startOfDay()->diffInDays($deadline, false);

            return $this->cachedKecepatanInfo = [
                'label' => $diffInDays < 0
                    ? 'Perlu Revisi (Lewat DL ' . abs($diffInDays) . ' hari)'
                    : ($diffInDays === 0 ? 'Perlu Revisi (DL Hari ini)' : "Perlu Revisi (Sisa $diffInDays hari)"),
                'color' => 'bg-amber-100 text-amber-800 border-amber-300',
                'skor_kecepatan' => max(0, min(80, $progress - 20)),
                'skor_ketepatan' => $diffInDays < 0 ? 40 : 80,
                'is_on_time' => false,
            ];
        }

        if (in_array($this->status, ['Completed', 'Reviewed'])) {
            $completionLog = $this->latestCompletionLog();
            $completionDate = $completionLog
                ? Carbon::parse($completionLog->tanggal_update)
                : Carbon::parse($this->updated_at);

            $diffInDays = (int) $completionDate->startOfDay()->diffInDays($deadline->startOfDay(), false);

            if ($diffInDays > 0) {
                return $this->cachedKecepatanInfo = [
                    'label' => "Sangat Cepat ($diffInDays hari sebelum DL)",
                    'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'skor_kecepatan' => 100,
                    'skor_ketepatan' => 100,
                    'is_on_time' => true,
                ];
            }

            if ($diffInDays === 0) {
                return $this->cachedKecepatanInfo = [
                    'label' => 'Tepat Waktu (Hari DL)',
                    'color' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'skor_kecepatan' => 95,
                    'skor_ketepatan' => 100,
                    'is_on_time' => true,
                ];
            }

            $daysLate = abs($diffInDays);

            return $this->cachedKecepatanInfo = [
                'label' => "Terlambat $daysLate hari dari DL",
                'color' => 'bg-red-50 text-red-700 border-red-200',
                'skor_kecepatan' => max(0, 100 - ($daysLate * 15)),
                'skor_ketepatan' => 50,
                'is_on_time' => false,
            ];
        }

        // Still pending or in progress. Timeliness and speed only count once work is submitted.
        $diffInDays = (int) now()->startOfDay()->diffInDays($deadline, false);

        if ($diffInDays < 0) {
            return $this->cachedKecepatanInfo = [
                'label' => 'Terlewat DL ' . abs($diffInDays) . ' hari (Belum Selesai)',
                'color' => 'bg-red-100 text-red-800 border-red-300',
                'skor_kecepatan' => 0,
                'skor_ketepatan' => 0,
                'is_on_time' => false,
            ];
        }

        return $this->cachedKecepatanInfo = [
            'label' => $diffInDays === 0 ? 'Batas DL Hari ini' : "Sisa $diffInDays hari sebelum DL",
            'color' => 'bg-amber-50 text-amber-700 border-amber-200',
            'skor_kecepatan' => 0,
            'skor_ketepatan' => 0,
            'is_on_time' => false,
        ];
    }

    /**
     * Method to calculate auto performance score based on progress and deadline.
     */
    public function calculateScore(): int
    {
        if ($this->evaluasi) {
            return (int) ($this->evaluasi->kualitas ?? $this->evaluasi->score ?? 0);
        }

        $progress = $this->persentase;

        if ($progress >= 100) {
            $completionLog = $this->latestCompletionLog();
            $completionDate = $completionLog
                ? Carbon::parse($completionLog->tanggal_update)
                : Carbon::parse($this->updated_at);
            $deadline = Carbon::parse($this->deadline)->startOfDay();

            return $completionDate->startOfDay()->lte($deadline) ? 100 : 70;
        }

        $deadline = Carbon::parse($this->deadline)->startOfDay();
        $now = now()->startOfDay();

        return $now->lte($deadline)
            ? $progress
            : (int) round($progress * 0.7);
    }

    /**
     * Clear cached computed values after checklists or progress are changed.
     */
    public function forgetComputedAttributes(): void
    {
        $this->cachedPersentase = null;
        $this->cachedKecepatanInfo = null;
    }

    private function latestProgressLog(): ?Progress
    {
        if ($this->relationLoaded('progress')) {
            return $this->progress->sortByDesc('tanggal_update')->first();
        }

        return $this->progress()->orderByDesc('tanggal_update')->first();
    }

    private function latestCompletionLog(): ?Progress
    {
        if ($this->relationLoaded('progress')) {
            return $this->progress
                ->filter(fn (Progress $log) => (int) $log->persentase >= 100)
                ->sortByDesc('tanggal_update')
                ->first();
        }

        return $this->progress()
            ->where('persentase', '>=', 100)
            ->orderByDesc('tanggal_update')
            ->first();
    }
}
