<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluasi', function (Blueprint $table) {
            $table->unsignedTinyInteger('kualitas')->nullable();
            $table->unsignedTinyInteger('komunikasi')->nullable();
            $table->unsignedTinyInteger('kerja_sama')->nullable();
            $table->unsignedTinyInteger('disiplin_tanggung_jawab')->nullable();
            $table->unsignedTinyInteger('problem_solving')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('evaluasi', function (Blueprint $table) {
            $table->dropColumn([
                'kualitas',
                'komunikasi',
                'kerja_sama',
                'disiplin_tanggung_jawab',
                'problem_solving',
            ]);
        });
    }
};