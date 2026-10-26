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
        if (! Schema::hasTable('student_activity_logs')) {
            Schema::create('student_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
                $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('activity_item_id')->constrained()->restrictOnDelete();
                $table->date('logged_on');
                $table->text('value')->nullable();
                $table->string('status')->default('submitted');
                $table->timestamps();

                $table->unique(['student_id', 'activity_item_id', 'logged_on']);
                $table->index(['school_id', 'class_id', 'logged_on', 'status']);
            });
        }

        if (! Schema::hasTable('activity_verifications')) {
            Schema::create('activity_verifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_activity_log_id')->constrained()->cascadeOnDelete();
                $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status')->default('pending');
                $table->text('notes')->nullable();
                $table->unsignedInteger('credited_points')->default(0);
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                $table->unique('student_activity_log_id');
                $table->index(['teacher_id', 'status']);
            });
        }

        if (! Schema::hasTable('student_points')) {
            Schema::create('student_points', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('activity_verification_id')->nullable()->constrained()->nullOnDelete();
                $table->integer('points');
                $table->string('description')->nullable();
                $table->timestamp('credited_at');
                $table->timestamps();

                $table->unique('activity_verification_id');
                $table->index(['student_id', 'semester_id', 'credited_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_points');
        Schema::dropIfExists('activity_verifications');
        Schema::dropIfExists('student_activity_logs');
    }
};
