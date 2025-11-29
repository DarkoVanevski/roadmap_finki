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
        Schema::table('subjects', function (Blueprint $table) {
            // Drop old columns
            if (Schema::hasColumn('subjects', 'semester')) {
                $table->dropColumn('semester');
            }

            // Add new columns
            if (!Schema::hasColumn('subjects', 'name_mk')) {
                $table->string('name_mk')->nullable()->after('name');
            }
            if (!Schema::hasColumn('subjects', 'description_mk')) {
                $table->text('description_mk')->nullable()->after('description');
            }
            if (!Schema::hasColumn('subjects', 'semester_type')) {
                $table->string('semester_type')->default('winter')->comment('winter or summer');
            }
            if (!Schema::hasColumn('subjects', 'year')) {
                $table->integer('year')->default(1)->comment('Year of study (1-4)');
            }
            if (!Schema::hasColumn('subjects', 'subject_type')) {
                $table->enum('subject_type', ['mandatory', 'elective'])->default('mandatory');
            }
            if (!Schema::hasColumn('subjects', 'instructors')) {
                $table->string('instructors')->nullable();
            }
            if (!Schema::hasColumn('subjects', 'total_hours')) {
                $table->integer('total_hours')->default(180);
            }
            if (!Schema::hasColumn('subjects', 'lecture_hours')) {
                $table->integer('lecture_hours')->default(30);
            }
            if (!Schema::hasColumn('subjects', 'practice_hours')) {
                $table->integer('practice_hours')->default(60);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            //
        });
    }
};
