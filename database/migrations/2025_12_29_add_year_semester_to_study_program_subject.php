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
        Schema::table('study_program_subject', function (Blueprint $table) {
            $table->integer('year')->default(1)->after('type')->comment('Year level of the subject in this program');
            $table->enum('semester_type', ['winter', 'summer'])->default('winter')->after('year')->comment('Semester type for this subject in this program');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_program_subject', function (Blueprint $table) {
            $table->dropColumn(['year', 'semester_type']);
        });
    }
};
