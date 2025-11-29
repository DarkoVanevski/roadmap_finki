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
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_mk')->comment('Name in Macedonian');
            $table->string('name_en')->nullable()->comment('Name in English');
            $table->text('description_mk')->nullable();
            $table->text('description_en')->nullable();
            $table->integer('duration_years')->comment('Duration in years (2, 3, or 4)');
            $table->enum('cycle', ['first', 'second', 'third'])->default('first')->comment('Study cycle');
            $table->string('code')->unique()->comment('Study program code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
