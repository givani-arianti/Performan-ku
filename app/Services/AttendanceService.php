<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\PengajuanIzin;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceService
{
    /**
     * Get attendance records for a user in the selected period.
     *
     * @param  string|int|null  $bulan
     * @param  string|int|null  $tahun
     */
    public function recordsFor(int $userId, $bulan, $tahun): Collection
    {
        $query = Absensi::where('user_id', $userId);
        $this->applyPeriod($query, 'tanggal', $bulan, $tahun);

        return $query->orderBy('tanggal')->orderBy('jam')->get();
    }

    /**
     * Build attendance numbers from a set of records.
     * Only real check-ins count as present; approved leave is tracked separately.
     */
    public function summarize(Collection $records, int $userId, $bulan, $tahun): array
    {
        $masuk = $records->where('jenis', 'masuk');

        return [
            'total_hadir' => $masuk->whereIn('status', ['Tepat waktu', 'Terlambat'])->count(),
            'tepat_waktu' => $masuk->where('status', 'Tepat waktu')->count(),
            'terlambat' => $masuk->where('status', 'Terlambat')->count(),
            'total_pulang' => $records->where('jenis', 'pulang')->count(),
            'izin_hari' => $this->approvedLeaveDays($userId, $bulan, $tahun),
        ];
    }

    /**
     * Count approved leave days (weekdays only) inside the selected period.
     */
    public function approvedLeaveDays(int $userId, $bulan, $tahun): int
    {
        $requests = PengajuanIzin::where('user_id', $userId)
            ->where('status', 'approved')
            ->get();

        [$periodStart, $periodEnd] = $this->periodBounds($bulan, $tahun);
        $dates = [];

        foreach ($requests as $izin) {
            $start = Carbon::parse($izin->tanggal_mulai)->startOfDay();
            $end = Carbon::parse($izin->tanggal_selesai)->startOfDay();

            if ($periodStart && $start->lt($periodStart)) {
                $start = $periodStart->copy();
            }

            if ($periodEnd && $end->gt($periodEnd)) {
                $end = $periodEnd->copy();
            }

            if ($start->gt($end)) {
                continue;
            }

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                if (! $date->isWeekend()) {
                    $dates[$date->toDateString()] = true;
                }
            }
        }

        return count($dates);
    }

    /**
     * Today's attendance statistics for the HR dashboard.
     */
    public function todayStats(int $totalEmployees): array
    {
        $records = Absensi::where('tanggal', now()->toDateString())
            ->where('jenis', 'masuk')
            ->get();

        $presentIds = $records->whereIn('status', ['Tepat waktu', 'Terlambat'])
            ->pluck('user_id')
            ->unique();
        $leaveIds = $records->whereIn('status', ['Cuti', 'Izin', 'Sakit'])
            ->pluck('user_id')
            ->unique();

        return [
            'present' => $presentIds->count(),
            'on_time' => $records->where('status', 'Tepat waktu')->count(),
            'late' => $records->where('status', 'Terlambat')->count(),
            'on_leave' => $leaveIds->count(),
            'not_checked_in' => max(0, $totalEmployees - $presentIds->count() - $leaveIds->count()),
        ];
    }

    /**
     * Apply the month/year filter used across attendance pages.
     */
    private function applyPeriod($query, string $column, $bulan, $tahun): void
    {
        if ($bulan && $bulan !== 'all') {
            $query->whereMonth($column, $bulan)->whereYear($column, $tahun);
        } elseif ($tahun && $tahun !== 'all') {
            $query->whereYear($column, $tahun);
        }
    }

    /**
     * Resolve the first and last day of the selected period.
     */
    private function periodBounds($bulan, $tahun): array
    {
        if ($bulan && $bulan !== 'all' && $tahun && $tahun !== 'all') {
            $start = Carbon::create((int) $tahun, (int) $bulan, 1)->startOfMonth();

            return [$start, $start->copy()->endOfMonth()];
        }

        if ($tahun && $tahun !== 'all') {
            $start = Carbon::create((int) $tahun, 1, 1)->startOfYear();

            return [$start, $start->copy()->endOfYear()];
        }

        return [null, null];
    }
}
