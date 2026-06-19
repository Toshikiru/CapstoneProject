<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bio_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('counselor_id')->constrained('users')->onDelete('cascade');
            $table->text('observation');
            $table->text('follow_up_actions')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['open', 'closed', 'follow_up'])->default('open');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bio_notes');
    }
};
