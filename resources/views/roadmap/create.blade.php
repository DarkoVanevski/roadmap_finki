<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="font-bold text-2xl text-gray-900 mb-2">Создајте Свој Академски Roadmap</h2>
                <p class="text-gray-600 mb-6">Додадете ги сите предмети за да го генерирате вашиот roadmap</p>

                <form method="POST" action="{{ route('roadmap.store') }}" class="space-y-8" id="roadmapForm">
                    @csrf

                    <!-- Step 1: Basic Info -->
                    <div class="bg-indigo-50 rounded-lg p-6 border-l-4 border-indigo-600">
                        <h3 class="font-bold text-lg text-gray-900 mb-4">Чекор 1: Основни информации</h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Study Program Selection -->
                            <div>
                                <label for="study_program_id" class="block font-bold text-sm text-gray-900 mb-2">
                                    Студиска Програма <span class="text-red-500">*</span>
                                </label>
                                <select id="study_program_id" name="study_program_id" class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" required>
                                    <option value="">-- Изберете студиска програма --</option>
                                    @forelse($studyPrograms as $program)
                                        <option value="{{ $program->id }}">
                                            {{ $program->name_mk }} ({{ $program->duration_years }} години)
                                        </option>
                                    @empty
                                        <option disabled>Нема достапни студиски програми</option>
                                    @endforelse
                                </select>
                                @error('study_program_id')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Current Year Selection -->
                            <div>
                                <label for="current_year" class="block font-bold text-sm text-gray-900 mb-2">
                                    Моја тековна година <span class="text-red-500">*</span>
                                </label>
                                <select id="current_year" name="current_year" class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" required>
                                    <option value="">-- Изберете година --</option>
                                    <option value="1">1-ва година</option>
                                    <option value="2">2-ра година</option>
                                    <option value="3">3-та година</option>
                                    <option value="4">4-та година</option>
                                </select>
                                @error('current_year')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-white rounded border border-indigo-200">
                            <p class="text-sm text-gray-600">
                                <strong class="text-indigo-600">Информација:</strong> Откако ќе го попуните овој чекор, може да ги додадете вашите завршени предмети. како и вашите предвидени предмети.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2: Subjects Selection (Hidden until step 1 is filled) -->
                    <div id="step2" style="display: none;">
                        <div class="bg-green-50 rounded-lg p-6 border-l-4 border-green-600">
                            <h3 class="font-bold text-lg text-gray-900 mb-6">Чекор 2: Внесете вашиот roadmap</h3>

                            <div class="mb-4 p-3 bg-white rounded border border-green-200">
                                <p class="text-sm text-gray-600">
                                    <strong class="text-green-600">Обврска:</strong> Мора да изберете барем еден предмет во една од категориите (завршен или во процес).
                                </p>
                            </div>

                            <!-- Search Box -->
                            <div class="mb-6">
                                <input type="text" id="subject-search" placeholder="🔍 Пребарај предмети по име или код..." class="w-full px-4 py-3 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                            </div>

                            <!-- Year Section Container will be populated by JavaScript -->
                            <div id="year-container">
                                <p class="text-gray-500 text-center py-4">Изберете студиска програма за да ги видите предметите...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between pt-6 border-t" id="submitContainer" style="display: none;">
                        <button type="reset" class="inline-flex items-center px-6 py-3 bg-gray-400 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Рестартирај
                        </button>
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Генерирај roadmap
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studyProgramSelect = document.getElementById('study_program_id');
    const currentYearSelect = document.getElementById('current_year');
    const searchInput = document.getElementById('subject-search');
    const step2 = document.getElementById('step2');
    const submitContainer = document.getElementById('submitContainer');
    const yearContainer = document.getElementById('year-container');

    let subjectsData = {}; // Store subjects for current program

    // Function to check if step 1 is complete
    function checkStep1Complete() {
        return studyProgramSelect.value !== '' && currentYearSelect.value !== '';
    }

    // Function to check if at least one subject is selected
    function checkStep2Complete() {
        const completedCheckboxes = document.querySelectorAll('input[name="completed_subjects[]"]:checked');
        const inProgressCheckboxes = document.querySelectorAll('input[name="in_progress_subjects[]"]:checked');
        return completedCheckboxes.length > 0 || inProgressCheckboxes.length > 0;
    }

    // Function to render subjects for a program
    function renderSubjects(subjects) {
        subjectsData = subjects;

        // Group subjects by year and semester
        const subjectsByYear = {};
        subjects.forEach(subject => {
            const year = subject.year;
            if (!subjectsByYear[year]) {
                subjectsByYear[year] = { winter: [], summer: [] };
            }
            subjectsByYear[year][subject.semester_type].push(subject);
        });

        // Build HTML
        let html = '';
        Object.keys(subjectsByYear).sort((a, b) => a - b).forEach(year => {
            const semesters = subjectsByYear[year];
            html += buildYearSection(year, semesters);
        });

        yearContainer.innerHTML = html;

        // Reattach event listeners to new checkboxes
        attachCheckboxListeners();

        // Attach year header toggle listeners
        attachYearToggleListeners();

        // Apply year filtering
        filterYearSections();
    }

    // Build year section HTML
    function buildYearSection(year, semesters) {
        let html = `<div class="mb-8 year-section" data-year="${year}" style="display: none;">
            <div class="bg-indigo-100 rounded-t-lg p-4 cursor-pointer flex items-center justify-between year-header hover:bg-indigo-200 transition" data-year="${year}">
                <h4 class="font-bold text-xl text-indigo-700">
                    Година ${year}
                </h4>
                <span class="year-toggle-icon text-indigo-700 text-2xl">▼</span>
            </div>
            <div class="year-content bg-white rounded-b-lg p-6 " style="display: block;">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">`;

        // Winter Semester
        html += buildSemesterSection('winter', semesters.winter, '❄️ Зимски Семестар', 'blue');

        // Summer Semester
        html += buildSemesterSection('summer', semesters.summer, '☀️ Летни Семестар', 'amber');

        html += `</div></div></div>`;
        return html;
    }

    // Build semester section HTML
    function buildSemesterSection(semesterType, subjects, title, color) {
        const mandatory = subjects.filter(s => s.subject_type === 'mandatory');
        const elective = subjects.filter(s => s.subject_type === 'elective');

        const colorClass = color === 'blue' ? 'bg-blue-50 border-l-4 border-blue-600 text-blue-700' : 'bg-amber-50 border-l-4 border-amber-600 text-amber-700';

        let html = `<div class="${colorClass} rounded-lg p-6">
            <h5 class="font-bold text-lg mb-6">${title}</h5>`;

        // Mandatory subjects
        if (mandatory.length > 0) {
            html += `<div class="mb-6">
                <h6 class="font-semibold text-sm text-green-700 mb-3">✓ Задолжителни предмети</h6>
                <div class="space-y-3 pl-2 mb-4">`;

            mandatory.forEach(subject => {
                html += buildSubjectCheckbox(subject, 'green');
            });

            html += `</div></div>`;
        }

        // Elective subjects
        if (elective.length > 0) {
            html += `<div class="border-t pt-4">
                <h6 class="font-semibold text-sm text-purple-700 mb-3">⭐ Изборни предмети</h6>
                <div class="space-y-3 pl-2">`;

            elective.forEach(subject => {
                html += buildSubjectCheckbox(subject, 'purple');
            });

            html += `</div></div>`;
        }

        if (mandatory.length === 0 && elective.length === 0) {
            html += `<p class="text-gray-500 text-sm">Нема предмети</p>`;
        }

        html += `</div>`;
        return html;
    }

    // Build individual subject checkbox
    function buildSubjectCheckbox(subject, type) {
        const colorClass = type === 'green' ? 'border-gray-300 text-green-600 focus:border-green-500 focus:ring-green-500' : 'border-gray-300 text-purple-600 focus:border-purple-500 focus:ring-purple-500';

        return `<div class="subject-item" data-code="${subject.code}" data-name="${subject.name.toLowerCase()} ${subject.name_mk.toLowerCase()}">
            <label class="flex items-start cursor-pointer group">
                <input type="checkbox" name="completed_subjects[]" value="${subject.id}"
                    class="completed-subject rounded ${colorClass} shadow-sm mt-1">
                <span class="ml-3 text-sm">
                    <strong class="text-gray-900">${subject.code}</strong>
                    <span class="block text-gray-600 text-xs">${subject.name}</span>
                    <span class="block text-gray-500 text-xs italic">${subject.name_mk}</span>
                    <span class="text-gray-500 text-xs">${subject.credits} ECTS</span>
                </span>
            </label>
        </div>`;
    }

    // Function to filter subjects by search
    function filterSubjectsBySearch() {
        const searchTerm = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const subjectItems = document.querySelectorAll('.subject-item');

        subjectItems.forEach(item => {
            const code = item.getAttribute('data-code') || '';
            const name = item.getAttribute('data-name') || '';

            if (searchTerm === '' || code.toLowerCase().includes(searchTerm) || name.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Function to filter year sections based on current year
    function filterYearSections() {
        const currentYear = parseInt(currentYearSelect.value) || 0;
        const yearSections = document.querySelectorAll('.year-section');

        yearSections.forEach(section => {
            const sectionYear = parseInt(section.getAttribute('data-year'));
            if (currentYear > 0 && sectionYear <= currentYear) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
                // Uncheck all checkboxes in hidden sections
                section.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            }
        });

        // Apply search filter after year filtering
        filterSubjectsBySearch();
    }

    // Attach checkbox listeners
    function attachCheckboxListeners() {
        const completedCheckboxes = document.querySelectorAll('input[name="completed_subjects[]"]');

        completedCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateForm();
            });
        });
    }

    // Attach year toggle listeners
    function attachYearToggleListeners() {
        const yearHeaders = document.querySelectorAll('.year-header');

        yearHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const yearContent = this.nextElementSibling;
                const toggleIcon = this.querySelector('.year-toggle-icon');

                if (yearContent.style.display === 'none') {
                    yearContent.style.display = 'block';
                    toggleIcon.textContent = '▼';
                } else {
                    yearContent.style.display = 'none';
                    toggleIcon.textContent = '▶';
                }
            });
        });
    }

    // Function to update visibility and form state
    function updateForm() {
        filterYearSections();
        if (checkStep1Complete()) {
            step2.style.display = 'block';
            submitContainer.style.display = checkStep2Complete() ? 'flex' : 'none';
        } else {
            step2.style.display = 'none';
            submitContainer.style.display = 'none';
        }
    }

    // Fetch subjects when study program is selected
    studyProgramSelect.addEventListener('change', async function() {
        if (this.value) {
            try {
                const response = await fetch(`/api/study-program/${this.value}/subjects`);
                const subjects = await response.json();
                renderSubjects(subjects);
            } catch (error) {
                console.error('Error fetching subjects:', error);
                yearContainer.innerHTML = '<p class="text-red-500">Грешка при вчитување на предметите</p>';
            }
        } else {
            yearContainer.innerHTML = '<p class="text-gray-500 text-center py-4">Изберете студиска програма за да ги видите предметите...</p>';
        }
        updateForm();
    });

    // Listen to year changes
    currentYearSelect.addEventListener('change', updateForm);

    // Listen to search input
    if (searchInput) {
        searchInput.addEventListener('input', filterSubjectsBySearch);
    }

    // Initial check
    updateForm();
});
</script>
</x-app-layout>
