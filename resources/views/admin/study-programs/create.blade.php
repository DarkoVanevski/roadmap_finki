<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="font-bold text-2xl text-gray-900 mb-6">Креирај нова студиска програма</h2>

                <form method="POST" action="{{ route('study-programs.store') }}" class="space-y-6">
                    @csrf

                    <!-- Names -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="name_mk" class="block text-sm font-bold text-gray-900 mb-2">Име (Македонски) <span class="text-red-500">*</span></label>
                            <input type="text" id="name_mk" name="name_mk" value="{{ old('name_mk') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('name_mk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="name_en" class="block text-sm font-bold text-gray-900 mb-2">Име (Англиски) <span class="text-red-500">*</span></label>
                            <input type="text" id="name_en" name="name_en" value="{{ old('name_en') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('name_en') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Code and Duration -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label for="code" class="block text-sm font-bold text-gray-900 mb-2">Код <span class="text-red-500">*</span></label>
                            <input type="text" id="code" name="code" value="{{ old('code') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="duration_years" class="block text-sm font-bold text-gray-900 mb-2">Трајање (години) <span class="text-red-500">*</span></label>
                            <select id="duration_years" name="duration_years" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Изберете --</option>
                                <option value="1" {{ old('duration_years') == '1' ? 'selected' : '' }}>1 година</option>
                                <option value="2" {{ old('duration_years') == '2' ? 'selected' : '' }}>2 години</option>
                                <option value="3" {{ old('duration_years') == '3' ? 'selected' : '' }}>3 години</option>
                                <option value="4" {{ old('duration_years') == '4' ? 'selected' : '' }}>4 години</option>
                                <option value="5" {{ old('duration_years') == '5' ? 'selected' : '' }}>5 години</option>
                                <option value="6" {{ old('duration_years') == '6' ? 'selected' : '' }}>6 години</option>
                            </select>
                            @error('duration_years') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="cycle" class="block text-sm font-bold text-gray-900 mb-2">Циклус <span class="text-red-500">*</span></label>
                            <select id="cycle" name="cycle" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Изберете --</option>
                                <option value="first" {{ old('cycle') == 'first' ? 'selected' : '' }}>Прв циклус</option>
                                <option value="second" {{ old('cycle') == 'second' ? 'selected' : '' }}>Втор циклус</option>
                            </select>
                            @error('cycle') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="description_mk" class="block text-sm font-bold text-gray-900 mb-2">Опис (Македонски)</label>
                            <textarea id="description_mk" name="description_mk" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description_mk') }}</textarea>
                            @error('description_mk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="description_en" class="block text-sm font-bold text-gray-900 mb-2">Опис (Англиски)</label>
                            <textarea id="description_en" name="description_en" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description_en') }}</textarea>
                            @error('description_en') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Subjects -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Предмети</label>
                        <div id="subjectsContainer" class="space-y-2">
                            <div class="grid grid-cols-12 gap-2 items-end">
                                <div class="col-span-5">
                                    <select class="subjectSelect w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" name="subjects[]" data-index="0">
                                        <option value="">-- Изберете предмет --</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <select class="subjectType w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" name="subject_types[0]">
                                        <option value="mandatory">Задолж.</option>
                                        <option value="elective">Изборен</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <select class="subjectYear w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" name="subject_years[0]">
                                        <option value="1">Година 1</option>
                                        <option value="2">Година 2</option>
                                        <option value="3">Година 3</option>
                                        <option value="4">Година 4</option>
                                        <option value="5">Година 5</option>
                                        <option value="6">Година 6</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <select class="subjectSemester w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" name="subject_semesters[0]">
                                        <option value="winter">Зимски</option>
                                        <option value="summer">Летен</option>
                                    </select>
                                </div>
                                <button type="button" onclick="removeSubject(this)" class="col-span-1 px-3 py-2 bg-red-100 text-red-700 rounded text-sm font-semibold hover:bg-red-200">Избриши</button>
                            </div>
                        </div>
                        <button type="button" onclick="addSubject()" class="mt-3 px-4 py-2 bg-green-100 text-green-700 rounded text-sm font-semibold hover:bg-green-200">+ Додади предмет</button>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between pt-6 border-t">
                        <a href="{{ route('study-programs.index') }}" class="px-6 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Откажи</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Креирај програма</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let subjectCount = 1;

    function addSubject() {
        const container = document.getElementById('subjectsContainer');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-12 gap-2 items-end';
        div.innerHTML = `
            <div class="col-span-5">
                <select class="subjectSelect w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" name="subjects[]" data-index="${subjectCount}">
                    <option value="">-- Изберете предмет --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <select class="subjectType w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" name="subject_types[${subjectCount}]">
                    <option value="mandatory">Задолж.</option>
                    <option value="elective">Изборен</option>
                </select>
            </div>
            <div class="col-span-2">
                <select class="subjectYear w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" name="subject_years[${subjectCount}]">
                    <option value="1">Година 1</option>
                    <option value="2">Година 2</option>
                    <option value="3">Година 3</option>
                    <option value="4">Година 4</option>
                    <option value="5">Година 5</option>
                    <option value="6">Година 6</option>
                </select>
            </div>
            <div class="col-span-2">
                <select class="subjectSemester w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" name="subject_semesters[${subjectCount}]">
                    <option value="winter">Зимски</option>
                    <option value="summer">Летен</option>
                </select>
            </div>
            <button type="button" onclick="removeSubject(this)" class="col-span-1 px-3 py-2 bg-red-100 text-red-700 rounded text-sm font-semibold hover:bg-red-200">Избриши</button>
        `;
        container.appendChild(div);
        subjectCount++;
    }

    function removeSubject(button) {
        button.parentElement.remove();
    }
</script>
</x-app-layout>
