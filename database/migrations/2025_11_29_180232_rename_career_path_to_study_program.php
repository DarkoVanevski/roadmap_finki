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
        // Drop old career_path_subject table if it exists
        if (Schema::hasTable('career_path_subject')) {
            Schema::drop('career_path_subject');
        }

        // Drop old career_paths table if it exists
        if (Schema::hasTable('career_paths')) {
            Schema::drop('career_paths');
        }

        // Create new study_program_subject table
        Schema::create('study_program_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->constrained('study_programs')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0)->comment('Suggested order within the program');
            $table->enum('type', ['mandatory', 'elective'])->default('mandatory');
            $table->timestamps();
            $table->unique(['study_program_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_program_subject');
    }
};
