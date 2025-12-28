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
        Schema::create('career_paths', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('career_path_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_path_id')->constrained('career_paths')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('order')->default(0)->comment('Suggested order to take the subject');
            $table->boolean('is_required')->default(true)->comment('Whether this subject is required for the career path');
            $table->timestamps();
            $table->unique(['career_path_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_path_subject');
        Schema::dropIfExists('career_paths');
    }
};
