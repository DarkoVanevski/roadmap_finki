<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="font-bold text-3xl text-gray-900">Уреди го твојот Roadmap</h2>
                        <p class="text-gray-600 mt-2">
                            <strong class="text-indigo-600">{{ $studyProgram->name_mk }}</strong>
                            <span class="text-gray-500">({{ $studyProgram->duration_years }} години)</span>
                        </p>
                        @if($careerPath)
                            <div class="mt-3 inline-block bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-semibold">
                                🎯 Кариерна патека: {{ $careerPath->name }}
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('roadmap.show') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        ← Назад на roadmap
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('roadmap.store') }}" id="roadmapForm">
                    @csrf

                    <!-- STEP 1: Select Program and Career Path -->
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <h3 class="font-bold text-lg text-gray-900 mb-6">Чекор 1: Избор Програма и Кариера</h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="study_program_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Студирана програма:
                                </label>
                                <select name="study_program_id" id="programSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                                    <option value="">-- Избери програма --</option>
                                    @foreach($studyPrograms as $program)
                                        <option value="{{ $program->id }}" {{ $program->id === $studyProgram->id ? 'selected' : '' }}>
                                            {{ $program->name_mk }} ({{ $program->duration_years }} години)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="career_path_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Кариерна патека (опционално):
                                </label>
                                <select name="career_path_id" id="careerPathSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="">-- Без патека --</option>
                                    @foreach($careerPaths as $path)
                                        <option value="{{ $path->id }}" {{ $careerPath && $path->id === $careerPath->id ? 'selected' : '' }}>
                                            {{ $path->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Select Completed Subjects -->
                    <div class="mb-8">
                        <h3 class="font-bold text-lg text-gray-900 mb-6">Чекор 2: Завршени Предмети</h3>
                        <div id="completedSubjectsContainer" class="space-y-4">
                            <p class="text-gray-600">Вчитување на предмети...</p>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-4">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700">
                            🔄 Регенерирај Roadmap
                        </button>
                        <a href="{{ route('roadmap.history') }}" class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-lg font-semibold text-gray-700 hover:bg-gray-300">
                            Откажи
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Преходни функции за избор на предмети - исти како во create.blade.php
const completedIds = @json($completedIds);

document.getElementById('programSelect').addEventListener('change', function() {
    const programId = this.value;

    if (!programId) {
        document.getElementById('completedSubjectsContainer').innerHTML = '<p class="text-gray-600">Прво избери програма</p>';
        return;
    }

    // Fetch subjects for this program
    fetch(`/api/study-program/${programId}/subjects`)
        .then(response => response.json())
        .then(data => {
            // The API returns the array directly, not wrapped in a subjects property
            displaySubjects(data);
        })
        .catch(error => console.error('Error:', error));
});

function displaySubjects(subjects) {
    // Group by year
    const subjectsByYear = {};

    subjects.forEach(subject => {
        if (!subjectsByYear[subject.year]) {
            subjectsByYear[subject.year] = {
                winter: [],
                summer: []
            };
        }

        const semester = subject.semester_type === 'winter' ? 'winter' : 'summer';
        subjectsByYear[subject.year][semester].push(subject);
    });

    // Display completed subjects
    let completedHtml = '';
    Object.keys(subjectsByYear).sort().forEach(year => {
        completedHtml += `
            <div class="mb-8">
                <div class="bg-indigo-100 rounded-lg p-4 mb-4">
                    <h4 class="font-bold text-lg text-indigo-700">Година ${year}</h4>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">`;

        // Winter Semester
        const winterSubjects = subjectsByYear[year].winter;
        completedHtml += `
                    <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-600">
                        <h5 class="font-bold text-lg text-blue-700 mb-4">❄️ Зимски семестар</h5>`;

        if (winterSubjects.length > 0) {
            completedHtml += '<div class="space-y-3">';
            winterSubjects.forEach(subject => {
                const isChecked = completedIds.includes(subject.id) ? 'checked' : '';
                completedHtml += `
                    <div class="flex items-start p-3 bg-white rounded border border-blue-200">
                        <input type="checkbox" name="completed_subjects[]" value="${subject.id}"
                               class="mt-1 rounded" ${isChecked}>
                        <div class="ml-3 flex-1">
                            <p class="font-bold text-gray-900">${subject.code}</p>
                            <p class="text-gray-700 text-sm">${subject.name}</p>
                            <span class="inline-block mt-1 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                ${subject.credits} ECTS
                            </span>
                        </div>
                    </div>`;
            });
            completedHtml += '</div>';
        } else {
            completedHtml += '<p class="text-gray-500 text-sm">Нема предмети</p>';
        }

        completedHtml += `
                    </div>`;

        // Summer Semester
        const summerSubjects = subjectsByYear[year].summer;
        completedHtml += `
                    <div class="bg-amber-50 rounded-lg p-6 border-l-4 border-amber-600">
                        <h5 class="font-bold text-lg text-amber-700 mb-4">☀️ Летен семестар</h5>`;

        if (summerSubjects.length > 0) {
            completedHtml += '<div class="space-y-3">';
            summerSubjects.forEach(subject => {
                const isChecked = completedIds.includes(subject.id) ? 'checked' : '';
                completedHtml += `
                    <div class="flex items-start p-3 bg-white rounded border border-amber-200">
                        <input type="checkbox" name="completed_subjects[]" value="${subject.id}"
                               class="mt-1 rounded" ${isChecked}>
                        <div class="ml-3 flex-1">
                            <p class="font-bold text-gray-900">${subject.code}</p>
                            <p class="text-gray-700 text-sm">${subject.name}</p>
                            <span class="inline-block mt-1 bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded">
                                ${subject.credits} ECTS
                            </span>
                        </div>
                    </div>`;
            });
            completedHtml += '</div>';
        } else {
            completedHtml += '<p class="text-gray-500 text-sm">Нема предмети</p>';
        }

        completedHtml += `
                    </div>
                </div>
            </div>`;
    });

    document.getElementById('completedSubjectsContainer').innerHTML = completedHtml;
}

// Load subjects on page load if program is pre-selected
window.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.getElementById('programSelect');
    if (programSelect.value) {
        programSelect.dispatchEvent(new Event('change'));
    }
});
</script>
</x-app-layout>
