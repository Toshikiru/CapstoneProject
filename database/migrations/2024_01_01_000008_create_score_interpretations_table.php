<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_interpretations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('label');
            $table->text('description')->nullable();
            $table->enum('admission_status', ['Passed', 'Conditional', 'Failed']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_interpretations');
    }
};
