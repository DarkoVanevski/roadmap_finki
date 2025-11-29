<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::orderBy('year')->orderBy('semester_type')->orderBy('code')->paginate(20);
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
        $prerequisites = Subject::whereNotIn('id', [$subject->id])->get();
        return view('admin.subjects.edit', compact('subject', 'prerequisites'));
    }

    /**
     * Update the specified resource in storage.
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
        ]);

        $prerequisiteIds = $request->input('prerequisites', []);

        $subject->update($validated);
        $subject->prerequisites()->sync($prerequisiteIds);

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
