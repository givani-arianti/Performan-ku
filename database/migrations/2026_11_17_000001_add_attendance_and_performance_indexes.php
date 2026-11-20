<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicate check-ins before enforcing one record per day/type.
        $duplicates = DB::table('absensis')
            ->select('user_id', 'tanggal', 'jenis', DB::raw('MIN(id) as keep_id'))
            ->groupBy('user_id', 'tanggal', 'jenis')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('absensis')
                ->where('user_id', $duplicate->user_id)
                ->where('tanggal', $duplicate->tanggal)
                ->where('jenis', $duplicate->jenis)
                ->where('id', '<>', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('absensis', function (Blueprint $table) {
            $table->unique(['user_id', 'tanggal', 'jenis'], 'absensis_user_date_type_unique');
            $table->index(['tanggal', 'jenis', 'status'], 'absensis_date_type_status_index');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['id_karyawan', 'deadline'], 'tasks_employee_deadline_index');
            $table->index(['deadline', 'status'], 'tasks_deadline_status_index');
            $table->index(['divisi', 'deadline'], 'tasks_division_deadline_index');
        });

        Schema::table('evaluasi', function (Blueprint $table) {
            $table->index('id_manajer', 'evaluasi_manager_index');
        });

        Schema::table('pengajuan_izins', function (Blueprint $table) {
            $table->index(['status', 'tanggal_mulai'], 'pengajuan_izins_status_start_index');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_izins', function (Blueprint $table) {
            $table->dropIndex('pengajuan_izins_status_start_index');
        });

        Schema::table('evaluasi', function (Blueprint $table) {
            $table->dropIndex('evaluasi_manager_index');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_employee_deadline_index');
            $table->dropIndex('tasks_deadline_status_index');
            $table->dropIndex('tasks_division_deadline_index');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('absensis_user_date_type_unique');
            $table->dropIndex('absensis_date_type_status_index');
        });
    }
};
