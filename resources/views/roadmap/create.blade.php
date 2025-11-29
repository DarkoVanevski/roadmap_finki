<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="font-bold text-2xl text-gray-900 mb-2">Создајте Своја Академска Дорога</h2>
                <p class="text-gray-600 mb-6">Изберете студиска програма и внесете предмети што сте ги завршиле</p>

                <form method="POST" action="{{ route('roadmap.store') }}" class="space-y-8">
                    @csrf

                    <!-- Study Program Selection -->
                    <div class="border-b pb-6">
                        <label for="study_program_id" class="block font-bold text-lg text-gray-900 mb-4">
                            Студиска Програма <span class="text-red-500">*</span>
                        </label>
                        <select id="study_program_id" name="study_program_id" class="mt-2 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" required>
                            <option value="">-- Изберете студиска програма --</option>
                            @forelse($studyPrograms as $program)
                                <option value="{{ $program->id }}">
                                    {{ $program->name_mk }} ({{ $program->duration_years }} години)
                                    @if($program->name_en)
                                        - {{ $program->name_en }}
                                    @endif
                                </option>
                            @empty
                                <option disabled>Нема достапни студиски програми</option>
                            @endforelse
                        </select>
                        @error('study_program_id')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subjects by Year -->
                    <div>
                        <h3 class="font-bold text-xl text-gray-900 mb-6">Внесете вашиот напредок</h3>
                        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                            @php
                                $subjectsByYear = [];
                                foreach($subjects as $subject) {
                                    $key = $subject->year . '_' . $subject->semester_type;
                                    if (!isset($subjectsByYear[$key])) {
                                        $subjectsByYear[$key] = [];
                                    }
                                    $subjectsByYear[$key][] = $subject;
                                }
                                ksort($subjectsByYear);
                            @endphp

                            @foreach($subjectsByYear as $yearSemester => $yearSubjects)
                                @php
                                    [$year, $semesterType] = explode('_', $yearSemester);
                                    $semesterLabel = $semesterType === 'winter' ? 'Зимски' : 'Летни';
                                @endphp
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                    <h4 class="font-bold text-lg text-indigo-600 mb-4">
                                        Година {{ $year }} - {{ $semesterLabel }} семестар
                                    </h4>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-3 text-green-700">
                                                ✓ Завршени предмети
                                            </label>
                                            <div class="space-y-2 pl-4">
                                                @forelse($yearSubjects as $subject)
                                                    <label class="flex items-start cursor-pointer group">
                                                        <input type="checkbox" name="completed_subjects[]" value="{{ $subject->id }}" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500 mt-1">
                                                        <span class="ml-3 text-sm">
                                                            <strong class="text-gray-900">{{ $subject->code }}</strong>
                                                            <span class="block text-gray-600">{{ $subject->name }}</span>
                                                            @if($subject->name_mk && $subject->name_mk !== $subject->name)
                                                                <span class="block text-gray-500 text-xs italic">{{ $subject->name_mk }}</span>
                                                            @endif
                                                            <span class="text-gray-500 text-xs">{{ $subject->credits }} ECTS</span>
                                                        </span>
                                                    </label>
                                                @empty
                                                    <p class="text-gray-500 text-sm">Нема предмети за овој период</p>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="border-t pt-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3 text-yellow-700">
                                                ⏳ Предмети во процес
                                            </label>
                                            <div class="space-y-2 pl-4">
                                                @forelse($yearSubjects as $subject)
                                                    <label class="flex items-start cursor-pointer group">
                                                        <input type="checkbox" name="in_progress_subjects[]" value="{{ $subject->id }}" class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 mt-1">
                                                        <span class="ml-3 text-sm">
                                                            <strong class="text-gray-900">{{ $subject->code }}</strong>
                                                            <span class="block text-gray-600">{{ $subject->name }}</span>
                                                            @if($subject->name_mk && $subject->name_mk !== $subject->name)
                                                                <span class="block text-gray-500 text-xs italic">{{ $subject->name_mk }}</span>
                                                            @endif
                                                            <span class="text-gray-500 text-xs">{{ $subject->credits }} ECTS</span>
                                                        </span>
                                                    </label>
                                                @empty
                                                    <p class="text-gray-500 text-sm">Нема предмети за овој период</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 border-t">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Генерирај дорога
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const completedCheckboxes = document.querySelectorAll('input[name="completed_subjects[]"]');
    const inProgressCheckboxes = document.querySelectorAll('input[name="in_progress_subjects[]"]');

    // Prevent selecting same subject for both completed and in progress
    completedCheckboxes.forEach((cb, index) => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                inProgressCheckboxes[index].checked = false;
            }
        });
    });

    inProgressCheckboxes.forEach((cb, index) => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                completedCheckboxes[index].checked = false;
            }
        });
    });
});
</script>
</x-app-layout>
