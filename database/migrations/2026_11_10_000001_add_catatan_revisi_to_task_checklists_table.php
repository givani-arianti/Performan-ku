<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambahkan kolom catatan_revisi ke tabel task_checklists
     * agar manajer bisa menulis catatan per poin checklist saat meminta revisi.
     */
    public function up(): void
    {
        Schema::table('task_checklists', function (Blueprint $table) {
            // Catatan revisi dari manajer untuk poin checklist ini (nullable)
            $table->text('catatan_revisi')->nullable()->after('is_completed');
            // Flag apakah poin ini ditandai manajer perlu direvisi
            $table->boolean('perlu_revisi')->default(false)->after('catatan_revisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_checklists', function (Blueprint $table) {
            $table->dropColumn(['catatan_revisi', 'perlu_revisi']);
        });
    }
};
