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

    public function calculatePerformanceKpi($tasks = null): array
    {
        $tasks = $tasks ?: $this->assignedTasks()->with(['progress', 'evaluasi'])->get();
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->whereIn('status', ['Completed', 'Reviewed']);
        $completedCount = $completedTasks->count();
        $onTimeCount = $completedTasks->filter(function ($task) {
            $completionLog = $task->progress->where('persentase', '>=', 100)->sortBy('tanggal_update')->first();
            $completionDate = $completionLog
                ? \Carbon\Carbon::parse($completionLog->tanggal_update)
                : \Carbon\Carbon::parse($task->updated_at);

            return $completionDate->startOfDay()->lte(\Carbon\Carbon::parse($task->deadline)->startOfDay());
        })->count();
        $evaluatedTasks = $tasks->filter(fn ($task) => $task->evaluasi);

        $targetScore = $totalTasks > 0 ? ($completedCount / $totalTasks) * 100 : 0;
        $timelinessScore = $completedCount > 0 ? ($onTimeCount / $completedCount) * 100 : 0;
        $qualityScore = $evaluatedTasks->count() > 0
            ? $evaluatedTasks->avg(fn ($task) => $task->evaluasi->kualitas ?? $task->evaluasi->score)
            : 0;
            
        // 1. Performance KPI (Skala 0-100)
        // Komponen: Target (40%), Ketepatan Waktu (30%), Kualitas (30%)
        $rawPerformance = ($targetScore * 0.40) + ($timelinessScore * 0.30) + ($qualityScore * 0.30);

        // 2. Competency / Soft Skill (Skala 0-100)
        // Komponen: Komunikasi (25%), Kerja Sama (25%), Disiplin/Tanggung Jawab (25%), Problem Solving (25%)
        $rawCompetency = $evaluatedTasks->count() > 0
            ? ($evaluatedTasks->avg(fn ($task) => $task->evaluasi->komunikasi ?? 0) * 0.25)
                + ($evaluatedTasks->avg(fn ($task) => $task->evaluasi->kerja_sama ?? 0) * 0.25)
                + ($evaluatedTasks->avg(fn ($task) => $task->evaluasi->disiplin_tanggung_jawab ?? 0) * 0.25)
                + ($evaluatedTasks->avg(fn ($task) => $task->evaluasi->problem_solving ?? 0) * 0.25)
            : 0;

        // 3. Absensi / Presensi (Skala 0-100)
        $absensiRecords = \App\Models\Absensi::where('user_id', $this->id)
            ->where('jenis', 'masuk')
            ->get();
        $totalAbsensi = $absensiRecords->count();
        $tepatWaktuCount = $absensiRecords->where('status', 'Tepat waktu')->count();
        $attendanceScore = $totalAbsensi > 0 ? ($tepatWaktuCount / $totalAbsensi) * 100 : 0;

        // Weighted KPI: Performance KPI (60%), Competency/Soft Skill (30%), Absensi (10%)
        $performanceScore = $rawPerformance * 0.60;
        $competencyScore = $rawCompetency * 0.30;
        $attendanceContribution = $attendanceScore * 0.10;
        $finalScore = $performanceScore + $competencyScore + $attendanceContribution;

        return [
            'raw_performance' => round($rawPerformance, 2),
            'raw_competency' => round($rawCompetency, 2),
            'target_score' => round($targetScore, 2),
            'timeliness_score' => round($timelinessScore, 2),
            'quality_score' => round($qualityScore, 2),
            'performance_score' => round($performanceScore, 2),
            'competency_score' => round($competencyScore, 2),
            'attendance_score' => round($attendanceScore, 2),
            'attendance_contribution' => round($attendanceContribution, 2),
            'final_score' => round($finalScore, 2),
            'category' => match (true) {
                $finalScore >= 85 => 'Sangat Baik',
                $finalScore >= 70 => 'Baik',
                $finalScore >= 55 => 'Cukup',
                default => 'Perlu Perbaikan',
            },
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
