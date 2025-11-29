<?php

namespace App\Http\Controllers;

use App\Models\StudyProgram;
use App\Models\Subject;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoadmapController extends Controller
{
    /**
     * Show the roadmap form where user inputs completed, current, and desired study program
     */
    public function create(): View
    {
        $studyPrograms = StudyProgram::all();
        $subjects = Subject::orderBy('year')->orderBy('semester_type')->get();

        return view('roadmap.create', [
            'studyPrograms' => $studyPrograms,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Generate and display the recommended roadmap
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'study_program_id' => 'required|exists:study_programs,id',
            'completed_subjects' => 'array',
            'completed_subjects.*' => 'exists:subjects,id',
            'in_progress_subjects' => 'array',
            'in_progress_subjects.*' => 'exists:subjects,id',
        ]);

        $studyProgram = StudyProgram::with('subjects')->findOrFail($validated['study_program_id']);
        $completedIds = $validated['completed_subjects'] ?? [];
        $inProgressIds = $validated['in_progress_subjects'] ?? [];

        $user = auth()->user();

        // Clear existing progress for this study program
        UserProgress::where('user_id', $user->id)
            ->where('study_program_id', $studyProgram->id)
            ->delete();

        // Save completed subjects
        foreach ($completedIds as $subjectId) {
            UserProgress::create([
                'user_id' => $user->id,
                'subject_id' => $subjectId,
                'study_program_id' => $studyProgram->id,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        // Save in-progress subjects
        foreach ($inProgressIds as $subjectId) {
            UserProgress::create([
                'user_id' => $user->id,
                'subject_id' => $subjectId,
                'study_program_id' => $studyProgram->id,
                'status' => 'in_progress',
            ]);
        }

        // Generate roadmap
        $roadmap = $this->generateRoadmap($user->id, $studyProgram, $completedIds, $inProgressIds);

        return view('roadmap.show', [
            'studyProgram' => $studyProgram,
            'roadmap' => $roadmap,
            'completed' => collect($completedIds),
            'inProgress' => collect($inProgressIds),
        ]);
    }

    /**
     * Generate recommended roadmap based on user progress and prerequisites
     */
    private function generateRoadmap($userId, StudyProgram $studyProgram, array $completedIds, array $inProgressIds)
    {
        $allSubjects = $studyProgram->subjects()->get();
        $remaining = [];

        foreach ($allSubjects as $subject) {
            if (!in_array($subject->id, $completedIds) && !in_array($subject->id, $inProgressIds)) {
                // Check if prerequisites are met
                $prerequisites = $subject->prerequisites()->get();
                $prerequisitesMet = true;

                if ($prerequisites->isNotEmpty()) {
                    foreach ($prerequisites as $prerequisite) {
                        // Check if prerequisite is completed
                        $completed = in_array($prerequisite->id, $completedIds);
                        if (!$completed) {
                            $prerequisitesMet = false;
                            break;
                        }
                    }
                }

                $remaining[] = [
                    'subject' => $subject,
                    'prerequisites' => $prerequisites,
                    'ready' => $prerequisitesMet,
                    'year' => $subject->year,
                    'type' => $subject->pivot->type ?? 'mandatory',
                ];
            }
        }

        // Sort by: ready first, then by year, then by semester type
        usort($remaining, function ($a, $b) {
            if ($a['ready'] !== $b['ready']) {
                return $a['ready'] ? -1 : 1;
            }
            if ($a['year'] !== $b['year']) {
                return $a['year'] <=> $b['year'];
            }
            return 0;
        });

        return $remaining;
    }

    /**
     * Show the user's current roadmap
     */
    public function show(): View
    {
        $user = auth()->user();
        $latestProgress = $user->progress()
            ->latest()
            ->first();

        if (!$latestProgress) {
            return redirect()->route('roadmap.create')->with('info', 'Please select a study program first.');
        }

        $studyProgram = $latestProgress->studyProgram;

        $completedIds = $user->progress()
            ->where('study_program_id', $studyProgram->id)
            ->where('status', 'completed')
            ->pluck('subject_id')
            ->toArray();

        $inProgressIds = $user->progress()
            ->where('study_program_id', $studyProgram->id)
            ->where('status', 'in_progress')
            ->pluck('subject_id')
            ->toArray();

        $roadmap = $this->generateRoadmap($user->id, $studyProgram, $completedIds, $inProgressIds);

        return view('roadmap.show', [
            'studyProgram' => $studyProgram,
            'roadmap' => $roadmap,
            'completed' => collect($completedIds),
            'inProgress' => collect($inProgressIds),
        ]);
    }
}
