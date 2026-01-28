<?php

namespace App\Http\Controllers;

use App\Models\CareerPath;
use App\Models\Roadmap;
use App\Models\StudyProgram;
use App\Models\Subject;
use App\Models\UserProgress;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoadmapController extends Controller
{
    use AuthorizesRequests;
    /**
     * Show the roadmap form where user inputs completed, current, and desired study program
     */
    public function create(): View
    {
        $studyPrograms = StudyProgram::all();
        $careerPaths = CareerPath::all();
        // Don't fetch subjects here - they'll be loaded dynamically via AJAX

        return view('roadmap.create', [
            'studyPrograms' => $studyPrograms,
            'careerPaths' => $careerPaths,
            'subjects' => [], // Empty initially, will be populated by JavaScript
        ]);
    }

    /**
     * Get subjects for a specific study program via AJAX
     */
    public function getSubjectsByProgram($programId)
    {
        $program = StudyProgram::findOrFail($programId);
        $subjects = $program->subjects()
            ->orderBy('year')
            ->orderBy('semester_type')
            ->get()
            ->filter(function ($subject) use ($program) {
                // Only include subjects within the program's duration
                return $subject->year <= $program->duration_years;
            })
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'code' => $subject->code,
                    'name' => $subject->name,
                    'name_mk' => $subject->name_mk,
                    'year' => $subject->year,
                    'semester_type' => $subject->getSemesterTypeFromCode(), // Use code-derived type
                    'subject_type' => $subject->pivot->type ?? 'mandatory', // Get type from pivot
                    'credits' => $subject->credits,
                ];
            });

        return response()->json($subjects);
    }

    /**
     * Generate and display the recommended roadmap
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'study_program_id' => 'required|exists:study_programs,id',
            'career_path_id' => 'nullable|exists:career_paths,id',
            'completed_subjects' => 'array',
            'completed_subjects.*' => 'exists:subjects,id',
            'in_progress_subjects' => 'array',
            'in_progress_subjects.*' => 'exists:subjects,id',
        ]);

        $studyProgram = StudyProgram::with('subjects')->findOrFail($validated['study_program_id']);
        $careerPath = $validated['career_path_id'] ? CareerPath::findOrFail($validated['career_path_id']) : null;
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
                'career_path_id' => $careerPath?->id,
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
                'career_path_id' => $careerPath?->id,
                'status' => 'in_progress',
            ]);
        }

        // Generate roadmap
        $roadmap = $this->generateRoadmap($user->id, $studyProgram, $completedIds, $inProgressIds, $careerPath);
        $semesterRoadmap = $this->generateSemesterRoadmap($studyProgram, $completedIds, $inProgressIds, $careerPath);

        // Save the generated roadmap to database
        $roadmapData = $this->serializeRoadmapData($roadmap);
        $semesterRoadmapData = $this->serializeSemesterRoadmapData($semesterRoadmap);

        Roadmap::updateOrCreate(
            [
                'user_id' => $user->id,
                'study_program_id' => $studyProgram->id,
            ],
            [
                'career_path_id' => $careerPath?->id,
                'roadmap_data' => $roadmapData,
                'semester_roadmap_data' => $semesterRoadmapData,
            ]
        );

        return view('roadmap.show', [
            'studyProgram' => $studyProgram,
            'careerPath' => $careerPath,
            'roadmap' => $roadmap,
            'semesterRoadmap' => $semesterRoadmap,
            'completed' => collect($completedIds),
            'inProgress' => collect($inProgressIds),
        ]);
    }

    /**
     * Generate recommended roadmap based on user progress and prerequisites
     */
    private function generateRoadmap($userId, StudyProgram $studyProgram, array $completedIds, array $inProgressIds, $careerPath = null)
    {
        $allSubjects = $studyProgram->subjects()->get();
        $remaining = [];

        // Get subject IDs for career path if one is selected
        $careerPathSubjectIds = $careerPath ? $careerPath->subjects()->pluck('subjects.id')->toArray() : [];

        foreach ($allSubjects as $subject) {
            // Skip subjects from years beyond the program duration
            if ($subject->year > $studyProgram->duration_years) {
                continue;
            }

            if (!in_array($subject->id, $completedIds) && !in_array($subject->id, $inProgressIds)) {
                // If career path is selected, filter to show:
                // 1. Mandatory subjects (always show)
                // 2. Electives related to the career path
                if ($careerPath) {
                    $isElective = ($subject->pivot->type ?? 'mandatory') === 'elective';
                    $isInCareerPath = in_array($subject->id, $careerPathSubjectIds);

                    // Skip electives not in the career path
                    if ($isElective && !$isInCareerPath) {
                        continue;
                    }
                }

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
                    'inCareerPath' => $careerPath && in_array($subject->id, $careerPathSubjectIds),
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

        // Get the latest roadmap for the user
        $latestRoadmap = Roadmap::where('user_id', $user->id)
            ->latest('updated_at')
            ->first();

        if (!$latestRoadmap) {
            return view('roadmap.no-roadmap');
        }

        $studyProgram = $latestRoadmap->studyProgram;
        $careerPath = $latestRoadmap->careerPath;

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

        $roadmap = $this->generateRoadmap($user->id, $studyProgram, $completedIds, $inProgressIds, $careerPath);
        $semesterRoadmap = $this->generateSemesterRoadmap($studyProgram, $completedIds, $inProgressIds, $careerPath);

        return view('roadmap.show', [
            'studyProgram' => $studyProgram,
            'careerPath' => $careerPath,
            'roadmap' => $roadmap,
            'semesterRoadmap' => $semesterRoadmap,
            'completed' => collect($completedIds),
            'inProgress' => collect($inProgressIds),
        ]);
    }

    /**
     * Show all roadmaps history for the user
     */
    public function history(): View
    {
        $user = auth()->user();
        $roadmaps = Roadmap::where('user_id', $user->id)
            ->with('studyProgram', 'careerPath')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('roadmap.history', [
            'roadmaps' => $roadmaps,
        ]);
    }

    /**
     * Edit an existing roadmap
     */
    public function edit(Roadmap $roadmap): View
    {
        $this->authorize('view', $roadmap);

        $user = auth()->user();
        $studyProgram = $roadmap->studyProgram;
        $careerPath = $roadmap->careerPath;

        // Get current completed and in-progress subject IDs
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

        // Get all study programs and career paths
        $studyPrograms = StudyProgram::all();
        $careerPaths = CareerPath::all();

        return view('roadmap.edit', [
            'roadmap' => $roadmap,
            'studyProgram' => $studyProgram,
            'careerPath' => $careerPath,
            'studyPrograms' => $studyPrograms,
            'careerPaths' => $careerPaths,
            'completedIds' => $completedIds,
            'inProgressIds' => $inProgressIds,
        ]);
    }

    /**
     * Delete a roadmap
     */
    public function destroy(Roadmap $roadmap)
    {
        $this->authorize('delete', $roadmap);

        $user = auth()->user();
        $studyProgram = $roadmap->studyProgram;

        // Delete the roadmap
        $roadmap->delete();

        // Delete associated user progress
        UserProgress::where('user_id', $user->id)
            ->where('study_program_id', $studyProgram->id)
            ->delete();

        return redirect()->route('roadmap.show')
            ->with('success', 'Roadmap deleted successfully.');
    }

    /**
     * Generate semester-by-semester roadmap for upcoming years
     */
    private function generateSemesterRoadmap(StudyProgram $studyProgram, array $completedIds, array $inProgressIds, $careerPath = null)
    {
        $allSubjects = $studyProgram->subjects()->orderBy('year')->orderBy('semester_type')->get();

        // Build roadmap organized by year and semester
        $roadmap = [];

        // Get subject IDs for career path if one is selected
        $careerPathSubjectIds = $careerPath ? $careerPath->subjects()->pluck('subjects.id')->toArray() : [];

        foreach ($allSubjects as $subject) {
            $year = $subject->year;
            $semester = $subject->getSemesterTypeFromCode() === 'winter' ? 'winter' : 'summer';

            // Skip subjects from years beyond the program duration
            if ($year > $studyProgram->duration_years) {
                continue;
            }

            // Skip if already completed or in progress
            if (in_array($subject->id, $completedIds) || in_array($subject->id, $inProgressIds)) {
                continue;
            }

            // If career path is selected, filter to show:
            // 1. Mandatory subjects (always show)
            // 2. Electives related to the career path
            if ($careerPath) {
                $isElective = ($subject->pivot->type ?? 'mandatory') === 'elective';
                $isInCareerPath = in_array($subject->id, $careerPathSubjectIds);

                // Skip electives not in the career path
                if ($isElective && !$isInCareerPath) {
                    continue;
                }
            }

            // Check prerequisites
            $prerequisites = $subject->prerequisites()->get();
            $prerequisitesMet = true;

            if ($prerequisites->isNotEmpty()) {
                foreach ($prerequisites as $prerequisite) {
                    if (!in_array($prerequisite->id, $completedIds)) {
                        $prerequisitesMet = false;
                        break;
                    }
                }
            }

            // Initialize year structure if needed
            if (!isset($roadmap[$year])) {
                $roadmap[$year] = [
                    'winter' => [],
                    'summer' => [],
                ];
            }

            $roadmap[$year][$semester][] = [
                'subject' => $subject,
                'ready' => $prerequisitesMet,
                'prerequisites' => $prerequisites,
            ];
        }

        return $roadmap;
    }

    /**
     * Serialize roadmap data for storage in JSON format
     */
    private function serializeRoadmapData(array $roadmap): array
    {
        return array_map(function ($item) {
            return [
                'subject_id' => $item['subject']->id,
                'subject_code' => $item['subject']->code,
                'subject_name' => $item['subject']->name,
                'subject_name_mk' => $item['subject']->name_mk,
                'prerequisites' => $item['prerequisites']->pluck('id')->toArray(),
                'ready' => $item['ready'],
                'year' => $item['year'],
                'type' => $item['type'],
                'inCareerPath' => $item['inCareerPath'],
                'credits' => $item['subject']->credits,
            ];
        }, $roadmap);
    }

    /**
     * Serialize semester roadmap data for storage in JSON format
     */
    private function serializeSemesterRoadmapData(array $semesterRoadmap): array
    {
        $serialized = [];
        foreach ($semesterRoadmap as $year => $semesters) {
            $serialized[$year] = [];
            foreach ($semesters as $semester => $items) {
                $serialized[$year][$semester] = array_map(function ($item) {
                    return [
                        'subject_id' => $item['subject']->id,
                        'subject_code' => $item['subject']->code,
                        'subject_name' => $item['subject']->name,
                        'subject_name_mk' => $item['subject']->name_mk,
                        'prerequisites' => $item['prerequisites']->pluck('id')->toArray(),
                        'ready' => $item['ready'],
                        'credits' => $item['subject']->credits,
                    ];
                }, $items);
            }
        }
        return $serialized;
    }
}
