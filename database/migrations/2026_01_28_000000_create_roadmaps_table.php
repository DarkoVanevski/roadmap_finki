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
        Schema::create('roadmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('study_program_id')->constrained('study_programs')->onDelete('cascade');
            $table->foreignId('career_path_id')->nullable()->constrained('career_paths')->onDelete('set null');
            $table->json('roadmap_data'); // Stores the generated roadmap suggestions
            $table->json('semester_roadmap_data'); // Stores semester-by-semester roadmap
            $table->timestamps();

            // Add index for quick lookups
            $table->index(['user_id', 'study_program_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roadmaps');
    }
};
