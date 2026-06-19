<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_correct')->nullable();
            $table->boolean('is_manually_graded')->default(false);
            $table->text('grader_remarks')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['exam_session_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
