<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Nota: Las citas reales se gestionan desde FastAPI.
        // Esta migración es solo para mantener estructura sincronizada.
        
        if (Schema::hasTable('citas') && !Schema::hasColumn('citas', 'deleted_at')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('citas') && Schema::hasColumn('citas', 'deleted_at')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
