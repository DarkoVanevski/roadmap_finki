<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('name_mk', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $subjects = $query->orderBy('year')->orderBy('semester_type')->orderBy('code')->paginate(20);

        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:subjects,code|max:20',
            'name' => 'required|string|max:255',
            'name_mk' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_mk' => 'nullable|string',
            'credits' => 'required|numeric|min:1|max:60',
            'year' => 'required|integer|between:1,4',
            'semester_type' => 'required|in:winter,summer',
            'subject_type' => 'required|in:mandatory,elective',
            'instructors' => 'nullable|string|max:255',
            'total_hours' => 'nullable|integer',
            'lecture_hours' => 'nullable|integer',
            'practice_hours' => 'nullable|integer',
        ]);

        Subject::create($validated);

        return redirect()->route('subjects.index')->with('success', 'Subject created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit(Subject $subject)
    {
        $prerequisites = Subject::whereNotIn('id', [$subject->id])->orderBy('year')->orderBy('code')->get();
        $careerPaths = \App\Models\CareerPath::all();
        return view('admin.subjects.edit', compact('subject', 'prerequisites', 'careerPaths'));    }

    /**     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'name_mk' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_mk' => 'nullable|string',
            'credits' => 'required|numeric|min:1|max:60',
            'year' => 'required|integer|between:1,4',
            'semester_type' => 'required|in:winter,summer',
            'subject_type' => 'required|in:mandatory,elective',
            'instructors' => 'nullable|string|max:255',
            'total_hours' => 'nullable|integer',
            'lecture_hours' => 'nullable|integer',
            'practice_hours' => 'nullable|integer',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:subjects,id',
            'career_paths' => 'nullable|array',
            'career_paths.*' => 'exists:career_paths,id',
        ]);

        $prerequisiteIds = $request->input('prerequisites', []);
        $careerPathIds = $request->input('career_paths', []);

        $subject->update($validated);
        $subject->prerequisites()->sync($prerequisiteIds);
        $subject->careerPaths()->sync($careerPathIds);

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->prerequisites()->detach();
        $subject->requiredBy()->detach();
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully');
    }
}
