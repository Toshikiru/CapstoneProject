<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_id_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->enum('sex', ['Male', 'Female']);
            $table->date('date_of_birth');
            $table->text('address');
            $table->string('contact_number', 20)->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_contact_number', 20)->nullable();
            $table->string('course');
            $table->string('year_level');
            $table->enum('admission_status', ['Pending', 'Passed', 'Conditional', 'Failed'])->default('Pending');
            $table->string('photo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
