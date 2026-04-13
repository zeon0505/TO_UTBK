<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $blueprint) {
            $blueprint->json('section_data')->nullable()->after('exam_id');
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $blueprint) {
            $blueprint->dropColumn('section_data');
        });
    }
};
