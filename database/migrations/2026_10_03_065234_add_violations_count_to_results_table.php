<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('results', 'violations_count')) {
            Schema::table('results', function (Blueprint $table) {
                $table->integer('violations_count')->default(0)->after('is_graded');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('results', 'violations_count')) {
            Schema::table('results', function (Blueprint $table) {
                $table->dropColumn('violations_count');
            });
        }
    }
};
