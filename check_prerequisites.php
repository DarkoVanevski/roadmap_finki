<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CURRICULUM IMPORT SUMMARY ===\n\n";

// Count subjects
$total = DB::table('subjects')->count();
$withPrereqs = DB::table('subjects')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
            ->from('subject_prerequisites')
            ->where('subject_prerequisites.subject_id', DB::raw('subjects.id'));
    })
    ->count();

echo "Total subjects: $total\n";
echo "Subjects with prerequisites: $withPrereqs\n\n";

// Count prerequisites
$totalPrereqs = DB::table('subject_prerequisites')->count();
echo "Total prerequisite relationships: $totalPrereqs\n\n";

// Show some examples of subjects with prerequisites
echo "--- Examples of subjects with prerequisites ---\n";
$examples = DB::table('subjects')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
            ->from('subject_prerequisites')
            ->whereRaw('subject_prerequisites.subject_id = subjects.id');
    })
    ->limit(5)
    ->get();

foreach ($examples as $subject) {
    echo "\n{$subject->code} - {$subject->name}\n";

    $prereqs = DB::table('subject_prerequisites')
        ->join('subjects', 'subject_prerequisites.prerequisite_id', '=', 'subjects.id')
        ->where('subject_prerequisites.subject_id', $subject->id)
        ->select('subjects.code', 'subjects.name')
        ->get();

    foreach ($prereqs as $prereq) {
        echo "  → Requires: {$prereq->code} ({$prereq->name})\n";
    }
}
