<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            // Simpan urutan soal acak agar konsisten setelah refresh
            $table->json('question_order')->nullable()->after('submitted_at');
            // Mode latihan: bisa latihan tanpa token, tidak dihitung ke nilai resmi
            $table->boolean('is_practice')->default(false)->after('question_order');
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['question_order', 'is_practice']);
        });
    }
};
