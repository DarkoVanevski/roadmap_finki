<?php

namespace App\Http\Controllers;

use App\Models\StudyProgram;
use App\Models\Subject;
use Illuminate\Http\Request;

class StudyProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StudyProgram::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name_mk', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $studyPrograms = $query->paginate(10);

        return view('admin.study-programs.index', compact('studyPrograms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::orderBy('year')->orderBy('code')->get();
        return view('admin.study-programs.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_mk' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|unique:study_programs,code|max:50',
            'description_mk' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration_years' => 'required|integer|between:1,6',
            'cycle' => 'required|in:first,second',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'subject_types.*' => 'in:mandatory,elective',
            'subject_years.*' => 'integer|between:1,6',
            'subject_semesters.*' => 'in:winter,summer',
        ]);

        $studyProgram = StudyProgram::create($validated);

        // Attach subjects with their types, years, and semesters
        if ($request->filled('subjects')) {
            $subjectData = [];
            foreach ($request->input('subjects') as $index => $subjectId) {
                $type = $request->input("subject_types.{$index}", 'elective');
                $year = $request->input("subject_years.{$index}", 1);
                $semester = $request->input("subject_semesters.{$index}", 'winter');
                $subjectData[$subjectId] = [
                    'type' => $type,
                    'year' => $year,
                    'semester_type' => $semester,
                    'order' => $index + 1
                ];
            }
            $studyProgram->subjects()->attach($subjectData);
        }

        return redirect()->route('study-programs.show', $studyProgram)->with('success', 'Study program created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudyProgram $studyProgram)
    {
        $studyProgram->load('subjects');
        $mandatorySubjects = $studyProgram->getMandatorySubjects();
        $electiveSubjects = $studyProgram->getElectiveSubjects();

        return view('admin.study-programs.show', compact('studyProgram', 'mandatorySubjects', 'electiveSubjects'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudyProgram $studyProgram)
    {
        $studyProgram->load('subjects');
        $allSubjects = Subject::orderBy('year')->orderBy('code')->get();
        return view('admin.study-programs.edit', compact('studyProgram', 'allSubjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudyProgram $studyProgram)
    {
        $validated = $request->validate([
            'name_mk' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:study_programs,code,' . $studyProgram->id,
            'description_mk' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration_years' => 'required|integer|between:1,6',
            'cycle' => 'required|in:first,second',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'subject_types.*' => 'in:mandatory,elective',
            'subject_years.*' => 'integer|between:1,6',
            'subject_semesters.*' => 'in:winter,summer',
        ]);

        $studyProgram->update($validated);

        // Sync subjects with their types, years, and semesters
        if ($request->filled('subjects')) {
            $subjectData = [];
            foreach ($request->input('subjects') as $index => $subjectId) {
                $type = $request->input("subject_types.{$index}", 'elective');
                $year = $request->input("subject_years.{$index}", 1);
                $semester = $request->input("subject_semesters.{$index}", 'winter');
                $subjectData[$subjectId] = [
                    'type' => $type,
                    'year' => $year,
                    'semester_type' => $semester,
                    'order' => $index + 1
                ];
            }
            $studyProgram->subjects()->sync($subjectData);
        } else {
            $studyProgram->subjects()->sync([]);
        }

        return redirect()->route('study-programs.show', $studyProgram)->with('success', 'Study program updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyProgram $studyProgram)
    {
        $studyProgram->subjects()->detach();
        $studyProgram->delete();

        return redirect()->route('study-programs.index')->with('success', 'Study program deleted successfully');
    }
}
