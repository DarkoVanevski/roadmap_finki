<x-app-layout>
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="font-bold text-2xl text-gray-900 mb-6">Уреди предмет</h2>

                <form method="POST" action="{{ route('subjects.update', $subject) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Code and Name -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="code" class="block text-sm font-bold text-gray-900 mb-2">Код <span class="text-red-500">*</span></label>
                            <input type="text" id="code" name="code" value="{{ old('code', $subject->code) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="credits" class="block text-sm font-bold text-gray-900 mb-2">ECTS <span class="text-red-500">*</span></label>
                            <input type="number" id="credits" name="credits" value="{{ old('credits', $subject->credits) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" max="60" required>
                            @error('credits') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Names -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-900 mb-2">Име (Англиски) <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $subject->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="name_mk" class="block text-sm font-bold text-gray-900 mb-2">Име (Македонски)</label>
                            <input type="text" id="name_mk" name="name_mk" value="{{ old('name_mk', $subject->name_mk) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name_mk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-900 mb-2">Опис (Англиски)</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $subject->description) }}</textarea>
                            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="description_mk" class="block text-sm font-bold text-gray-900 mb-2">Опис (Македонски)</label>
                            <textarea id="description_mk" name="description_mk" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description_mk', $subject->description_mk) }}</textarea>
                            @error('description_mk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Year and Semester -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label for="year" class="block text-sm font-bold text-gray-900 mb-2">Година <span class="text-red-500">*</span></label>
                            <select id="year" name="year" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Изберете --</option>
                                <option value="1" {{ old('year', $subject->year) == '1' ? 'selected' : '' }}>1-ва година</option>
                                <option value="2" {{ old('year', $subject->year) == '2' ? 'selected' : '' }}>2-ра година</option>
                                <option value="3" {{ old('year', $subject->year) == '3' ? 'selected' : '' }}>3-та година</option>
                                <option value="4" {{ old('year', $subject->year) == '4' ? 'selected' : '' }}>4-та година</option>
                            </select>
                            @error('year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="semester_type" class="block text-sm font-bold text-gray-900 mb-2">Семестар <span class="text-red-500">*</span></label>
                            <select id="semester_type" name="semester_type" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Изберете --</option>
                                <option value="winter" {{ old('semester_type', $subject->semester_type) == 'winter' ? 'selected' : '' }}>Зимски</option>
                                <option value="summer" {{ old('semester_type', $subject->semester_type) == 'summer' ? 'selected' : '' }}>Летен</option>
                            </select>
                            @error('semester_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="subject_type" class="block text-sm font-bold text-gray-900 mb-2">Тип <span class="text-red-500">*</span></label>
                            <select id="subject_type" name="subject_type" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Изберете --</option>
                                <option value="mandatory" {{ old('subject_type', $subject->subject_type) == 'mandatory' ? 'selected' : '' }}>Задолжителен</option>
                                <option value="elective" {{ old('subject_type', $subject->subject_type) == 'elective' ? 'selected' : '' }}>Изборен</option>
                            </select>
                            @error('subject_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Hours -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label for="total_hours" class="block text-sm font-bold text-gray-900 mb-2">Вкупно часови</label>
                            <input type="number" id="total_hours" name="total_hours" value="{{ old('total_hours', $subject->total_hours) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                            @error('total_hours') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="lecture_hours" class="block text-sm font-bold text-gray-900 mb-2">Часови предавања</label>
                            <input type="number" id="lecture_hours" name="lecture_hours" value="{{ old('lecture_hours', $subject->lecture_hours) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                            @error('lecture_hours') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="practice_hours" class="block text-sm font-bold text-gray-900 mb-2">Часови вежби</label>
                            <input type="number" id="practice_hours" name="practice_hours" value="{{ old('practice_hours', $subject->practice_hours) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                            @error('practice_hours') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Instructors -->
                    <div>
                        <label for="instructors" class="block text-sm font-bold text-gray-900 mb-2">Професори</label>
                        <input type="text" id="instructors" name="instructors" value="{{ old('instructors', $subject->instructors) }}" placeholder="Име1, Име2" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('instructors') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Prerequisites -->
                    <div>
                        <label for="prerequisites" class="block text-sm font-bold text-gray-900 mb-2">Предуслови</label>
                        <select id="prerequisites" name="prerequisites[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($prerequisites as $prereq)
                                <option value="{{ $prereq->id }}" {{ $subject->prerequisites->contains($prereq->id) ? 'selected' : '' }}>
                                    {{ $prereq->code }} - {{ $prereq->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-gray-500 text-sm mt-1">Задржите Ctrl/Cmd за да изберете повеќе</p>
                        @error('prerequisites') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between pt-6 border-t">
                        <a href="{{ route('subjects.index') }}" class="px-6 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Откажи</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Ажурирај предмет</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
