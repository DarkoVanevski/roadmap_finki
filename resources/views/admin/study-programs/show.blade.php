<x-app-layout>
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">{{ $studyProgram->name_mk }}</h2>
                        <p class="text-gray-600">{{ $studyProgram->name_en }} ({{ $studyProgram->code }})</p>
                        <div class="mt-2 flex gap-3">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm font-semibold">{{ $studyProgram->duration_years }} години</span>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm font-semibold">{{ ucfirst($studyProgram->cycle) }} циклус</span>
                        </div>
                    </div>
                    <div class="space-x-2">
                        <a href="{{ route('study-programs.edit', $studyProgram) }}" class="inline-block px-4 py-2 bg-yellow-100 text-yellow-700 rounded font-semibold hover:bg-yellow-200">Уреди</a>
                        <form method="POST" action="{{ route('study-programs.destroy', $studyProgram) }}" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure?')" class="px-4 py-2 bg-red-100 text-red-700 rounded font-semibold hover:bg-red-200">Избриши</button>
                        </form>
                        <a href="{{ route('study-programs.index') }}" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded font-semibold hover:bg-gray-200">Назад</a>
                    </div>
                </div>

                @if($studyProgram->description_mk)
                    <div class="mb-6 p-4 bg-gray-50 rounded border border-gray-200">
                        <p class="text-gray-700">{{ $studyProgram->description_mk }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mandatory Subjects -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Задолжителни предмети ({{ $mandatorySubjects->count() }})</h3>
                        <div class="space-y-2">
                            @forelse($mandatorySubjects as $subject)
                                <div class="p-3 bg-purple-50 border border-purple-200 rounded">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $subject->code }}</p>
                                            <p class="text-sm text-gray-600">{{ $subject->name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Година {{ $subject->pivot->year }} •
                                                {{ $subject->pivot->semester_type === 'winter' ? 'Зимски' : 'Летен' }} семестар •
                                                {{ $subject->credits }} ECTS
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Нема задолжителни предмети</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Elective Subjects -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Изборни предмети ({{ $electiveSubjects->count() }})</h3>
                        <div class="space-y-2">
                            @forelse($electiveSubjects as $subject)
                                <div class="p-3 bg-orange-50 border border-orange-200 rounded">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $subject->code }}</p>
                                            <p class="text-sm text-gray-600">{{ $subject->name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Година {{ $subject->pivot->year }} •
                                                {{ $subject->pivot->semester_type === 'winter' ? 'Зимски' : 'Летен' }} семестар •
                                                {{ $subject->credits }} ECTS
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Нема изборни предмети</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded border border-gray-200">
                        <p class="text-gray-600 text-sm">Вкупно предмети</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $studyProgram->subjects->count() }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded border border-purple-200">
                        <p class="text-gray-600 text-sm">Задолжителни</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $mandatorySubjects->count() }}</p>
                    </div>
                    <div class="p-4 bg-orange-50 rounded border border-orange-200">
                        <p class="text-gray-600 text-sm">Изборни</p>
                        <p class="text-2xl font-bold text-orange-900">{{ $electiveSubjects->count() }}</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded border border-blue-200">
                        <p class="text-gray-600 text-sm">Вкупно ECTS</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $studyProgram->subjects->sum('credits') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
