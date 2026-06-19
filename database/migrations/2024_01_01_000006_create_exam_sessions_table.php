<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->string('session_token', 64)->unique();
            $table->enum('status', ['in_progress', 'submitted', 'timed_out', 'invalidated'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('time_remaining')->nullable();
            $table->decimal('raw_score', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('interpretation')->nullable();
            $table->enum('result_status', ['Passed', 'Conditional', 'Failed', 'Pending'])->default('Pending');
            $table->boolean('is_graded')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->string('browser_info')->nullable();
            $table->integer('focus_loss_count')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
