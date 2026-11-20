<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'divisi',
    ];

    /**
     * Get tasks assigned to this user (employee).
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'id_karyawan');
    }

    /**
     * Get tasks created by this user (HR).
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'id_hr');
    }

    /**
     * Get evaluations made by this user (Manager).
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluasi::class, 'id_manajer');
    }

    public function komentarDikirim()
    {
        return $this->hasMany(Komentar::class, 'pengirim_id');
    }

    public function komentarDiterima()
    {
        return $this->hasMany(Komentar::class, 'penerima_id');
    }

    public function pengajuanIzin()
    {
        return $this->hasMany(PengajuanIzin::class, 'user_id');
    }

    public function calculatePerformanceKpi($tasks = null, $month = null, $year = null): array
    {
        $month = $month ?: now()->month;
        $year = $year ?: now()->year;

        if ($tasks === null) {
            $tasks = $this->assignedTasks()
                ->with(['progress', 'checklists'])
                ->whereMonth('deadline', $month)
                ->whereYear('deadline', $year)
                ->get();
        }

        $totalTasks = $tasks->count();

        if ($totalTasks === 0) {
            return [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'target_score' => 0,
                'timeliness_score' => 0,
                'speed_score' => 0,
                'final_score' => 0,
                'category' => 'Belum Ada Tugas',
                'month' => $month,
                'year' => $year,
            ];
        }

        $completedTasks = $tasks->filter(function($task) {
            return in_array($task->status, ['Completed', 'Reviewed']) || ($task->status !== 'Revisi' && $task->persentase >= 100);
        });
        $completedCount = $completedTasks->count();

        // 1. Kuantitas / Persentase pengerjaan dari seluruh tugas dalam 1 bulan (Weight: 40%)
        $totalProgressSum = $tasks->sum(fn($t) => $t->persentase);
        $targetScore = round($totalProgressSum / $totalTasks, 2);

        // 2. Ketepatan Waktu Sesuai DL (Weight: 30%)
        $totalTimelinessSum = $tasks->sum(fn($t) => $t->kecepatan_info['skor_ketepatan']);
        $timelinessScore = round($totalTimelinessSum / $totalTasks, 2);

        // 3. Kecepatan Pengerjaan Sesuai DL (Weight: 30%)
        $totalSpeedSum = $tasks->sum(fn($t) => $t->kecepatan_info['skor_kecepatan']);
        $speedScore = round($totalSpeedSum / $totalTasks, 2);

        // Final score: Akumulasi penilaian dari keseluruhan tugas dalam 1 bulan
        $finalScore = round(($targetScore * 0.40) + ($timelinessScore * 0.30) + ($speedScore * 0.30), 2);

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedCount,
            'target_score' => $targetScore,
            'timeliness_score' => $timelinessScore,
            'speed_score' => $speedScore,
            'final_score' => $finalScore,
            'category' => match (true) {
                $finalScore >= 85 => 'Sangat Baik',
                $finalScore >= 70 => 'Baik',
                $finalScore >= 55 => 'Cukup',
                default => 'Perlu Perbaikan',
            },
            'month' => $month,
            'year' => $year,
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
