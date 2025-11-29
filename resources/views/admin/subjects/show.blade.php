<x-app-layout>
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">{{ $subject->name }}</h2>
                        <p class="text-gray-600 mt-1">{{ $subject->name_mk }}</p>
                    </div>
                    <div class="space-x-2">
                        <a href="{{ route('subjects.edit', $subject) }}" class="inline-block px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Уреди</a>
                        <a href="{{ route('subjects.index') }}" class="inline-block px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Назад</a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-600">Код</p>
                        <p class="font-bold text-lg text-gray-900 font-mono">{{ $subject->code }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <p class="text-sm text-gray-600">ECTS кредити</p>
                        <p class="font-bold text-lg text-gray-900">{{ $subject->credits }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <p class="text-sm text-gray-600">Година</p>
                        <p class="font-bold text-lg text-gray-900">{{ $subject->year }}-та година</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                        <p class="text-sm text-gray-600">Семестар</p>
                        <p class="font-bold text-lg text-gray-900">{{ $subject->semester_type === 'winter' ? 'Зимски' : 'Летен' }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Тип:</p>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $subject->subject_type === 'mandatory' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $subject->subject_type === 'mandatory' ? 'Задолжителен' : 'Изборен' }}
                    </span>
                </div>

                @if($subject->description)
                    <div class="mb-6">
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Опис (Англиски)</h3>
                        <p class="text-gray-700">{{ $subject->description }}</p>
                    </div>
                @endif

                @if($subject->description_mk)
                    <div class="mb-6">
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Опис (Македонски)</h3>
                        <p class="text-gray-700">{{ $subject->description_mk }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-4 mb-6">
                    @if($subject->total_hours)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-600">Вкупно часови</p>
                            <p class="font-bold text-lg text-gray-900">{{ $subject->total_hours }}</p>
                        </div>
                    @endif
                    @if($subject->lecture_hours)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-600">Часови предавања</p>
                            <p class="font-bold text-lg text-gray-900">{{ $subject->lecture_hours }}</p>
                        </div>
                    @endif
                    @if($subject->practice_hours)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-600">Часови вежби</p>
                            <p class="font-bold text-lg text-gray-900">{{ $subject->practice_hours }}</p>
                        </div>
                    @endif
                </div>

                @if($subject->instructors)
                    <div class="mb-6">
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Професори</h3>
                        <p class="text-gray-700">{{ $subject->instructors }}</p>
                    </div>
                @endif

                @if($subject->prerequisites->count() > 0)
                    <div class="mb-6">
                        <h3 class="font-bold text-lg text-gray-900 mb-4">Предуслови</h3>
                        <div class="space-y-2">
                            @foreach($subject->prerequisites as $prereq)
                                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                    <p class="font-semibold text-gray-900">{{ $prereq->code }} - {{ $prereq->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $prereq->name_mk }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>
