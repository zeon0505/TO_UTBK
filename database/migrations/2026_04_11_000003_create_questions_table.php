<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice');
            $table->longText('question_text');
            $table->string('image')->nullable();
            $table->decimal('weight', 8, 2)->default(10.00);
            $table->text('explanation')->nullable(); // Rubrik / Kunci Jawaban / Pembahasan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
