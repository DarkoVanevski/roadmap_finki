<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="font-bold text-3xl text-gray-900">Вашиот Академски Roadmap</h2>
                        <p class="text-gray-600 mt-2 text-lg">
                            <strong class="text-indigo-600">{{ $studyProgram->name_mk }}</strong>
                            <span class="text-gray-500">({{ $studyProgram->duration_years }} години)</span>
                        </p>
                        @if($studyProgram->name_en)
                            <p class="text-gray-500 italic">{{ $studyProgram->name_en }}</p>
                        @endif
                    </div>
                    <a href="{{ route('roadmap.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300">
                        Уреди roadmap
                    </a>
                </div>
            </div>
        </div>

        <!-- Progress Summary -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-6">
            <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-l-4 border-green-500">
                    <p class="text-gray-500 text-sm">Завршени</p>
                    <p class="text-3xl font-bold text-green-600">{{ count($completed) }}</p>
                </div>
            </div>
            <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-l-4 border-yellow-500">
                    <p class="text-gray-500 text-sm">Во процес</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ count($inProgress) }}</p>
                </div>
            </div>
            <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-l-4 border-blue-500">
                    <p class="text-gray-500 text-sm">Преостаток</p>
                    @php
                        // Count only mandatory remaining subjects
                        $remainingMandatory = count(array_filter($roadmap, fn($item) => $item['subject']->subject_type === 'mandatory'));
                    @endphp
                    <p class="text-3xl font-bold text-blue-600">{{ $remainingMandatory }}</p>
                </div>
            </div>
            <div class="bg-purple-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-l-4 border-purple-500">
                    <p class="text-gray-500 text-sm">Вкупно ECTS</p>
                    @php
                        // Calculate ECTS needed based on program duration (60 ECTS per year)
                        $totalEctsRequired = $studyProgram->duration_years * 60;
                    @endphp
                    <p class="text-3xl font-bold text-purple-600">{{ $totalEctsRequired }}</p>
                </div>
            </div>
        </div>

        <!-- ECTS Progress Bar -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="font-bold text-lg text-gray-900 mb-4">Напредок во ECTS кредити</h3>
                @php
                    $completedEcts = 0;
                    $inProgressEcts = 0;

                    foreach($completed as $subjectId) {
                        $subject = \App\Models\Subject::find($subjectId);
                        if($subject) {
                            $completedEcts += $subject->credits ?? 0;
                        }
                    }

                    foreach($inProgress as $subjectId) {
                        $subject = \App\Models\Subject::find($subjectId);
                        if($subject) {
                            $inProgressEcts += $subject->credits ?? 0;
                        }
                    }

                    $totalEctsProgress = $completedEcts + $inProgressEcts;
                    $remainingEcts = $totalEctsRequired - $totalEctsProgress;
                    $progressPercent = $totalEctsRequired > 0 ? round(($totalEctsProgress / $totalEctsRequired) * 100) : 0;
                @endphp

                <div class="mb-2 flex justify-between text-sm">
                    <span class="text-gray-700"><strong>{{ $totalEctsProgress }} / {{ $totalEctsRequired }} ECTS</strong></span>
                    <span class="text-gray-600">{{ $progressPercent }}%</span>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-blue-500 h-3 rounded-full" style="width: {{ $progressPercent }}%"></div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-4 text-sm">
                    <div class="bg-green-50 p-3 rounded border border-green-200">
                        <p class="text-gray-600 text-xs">Завршено</p>
                        <p class="font-bold text-green-600">{{ $completedEcts }} ECTS</p>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                        <p class="text-gray-600 text-xs">Во процес</p>
                        <p class="font-bold text-yellow-600">{{ $inProgressEcts }} ECTS</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded border border-blue-200">
                        <p class="text-gray-600 text-xs">Преостаток</p>
                        <p class="font-bold text-blue-600">{{ $remainingEcts }} ECTS</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semester-by-Semester Roadmap -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="font-bold text-2xl text-gray-900 mb-8">📅 Предложен roadmap по години и семестри</h3>

                @if(count($semesterRoadmap) > 0)
                    <div class="space-y-8">
                        @foreach($semesterRoadmap as $year => $semesters)
                            <div class="border-l-4 border-indigo-600 pl-6 py-4">
                                <h4 class="font-bold text-xl text-indigo-700 mb-6">Година {{ $year }}</h4>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Winter Semester -->
                                    @if(count($semesters['winter']) > 0)
                                        <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-600">
                                            <h5 class="font-bold text-lg text-blue-700 mb-4">❄️ Зимски семестар</h5>
                                            <div class="space-y-3">
                                                @foreach($semesters['winter'] as $item)
                                                    <div class="bg-white p-3 rounded border {{ $item['ready'] ? 'border-green-300' : 'border-gray-300' }}">
                                                        <div class="flex justify-between items-start">
                                                            <div class="flex-1">
                                                                <p class="font-bold text-gray-900">{{ $item['subject']->code }}</p>
                                                                <p class="text-gray-700 text-sm">{{ $item['subject']->name }}</p>
                                                                @if($item['subject']->name_mk && $item['subject']->name_mk !== $item['subject']->name)
                                                                    <p class="text-gray-600 text-xs italic">{{ $item['subject']->name_mk }}</p>
                                                                @endif
                                                                <p class="text-gray-500 text-xs mt-2">{{ $item['subject']->credits }} ECTS</p>
                                                            </div>
                                                            <span class="text-sm font-semibold {{ $item['ready'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-2 py-1 rounded whitespace-nowrap ml-2">
                                                                {{ $item['ready'] ? '✓ Подготвено' : '🔒 Заблокирано' }}
                                                            </span>
                                                        </div>
                                                        @if(!$item['ready'] && $item['prerequisites']->isNotEmpty())
                                                            <div class="mt-2 text-xs text-red-700 bg-red-50 p-2 rounded">
                                                                <p class="font-semibold">Потребни предуслови:</p>
                                                                <ul class="list-disc list-inside">
                                                                    @foreach($item['prerequisites'] as $prereq)
                                                                        <li class="{{ in_array($prereq->id, $completed->toArray()) ? 'line-through text-gray-500' : '' }}">{{ $prereq->code }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-600 opacity-60">
                                            <h5 class="font-bold text-lg text-blue-700 mb-2">❄️ Зимски семестар</h5>
                                            <p class="text-gray-500 text-sm">Нема предмети</p>
                                        </div>
                                    @endif

                                    <!-- Summer Semester -->
                                    @if(count($semesters['summer']) > 0)
                                        <div class="bg-amber-50 rounded-lg p-6 border-l-4 border-amber-600">
                                            <h5 class="font-bold text-lg text-amber-700 mb-4">☀️ Летни семестар</h5>
                                            <div class="space-y-3">
                                                @foreach($semesters['summer'] as $item)
                                                    <div class="bg-white p-3 rounded border {{ $item['ready'] ? 'border-green-300' : 'border-gray-300' }}">
                                                        <div class="flex justify-between items-start">
                                                            <div class="flex-1">
                                                                <p class="font-bold text-gray-900">{{ $item['subject']->code }}</p>
                                                                <p class="text-gray-700 text-sm">{{ $item['subject']->name }}</p>
                                                                @if($item['subject']->name_mk && $item['subject']->name_mk !== $item['subject']->name)
                                                                    <p class="text-gray-600 text-xs italic">{{ $item['subject']->name_mk }}</p>
                                                                @endif
                                                                <p class="text-gray-500 text-xs mt-2">{{ $item['subject']->credits }} ECTS</p>
                                                            </div>
                                                            <span class="text-sm font-semibold {{ $item['ready'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-2 py-1 rounded whitespace-nowrap ml-2">
                                                                {{ $item['ready'] ? '✓ Подготвено' : '🔒 Заблокирано' }}
                                                            </span>
                                                        </div>
                                                        @if(!$item['ready'] && $item['prerequisites']->isNotEmpty())
                                                            <div class="mt-2 text-xs text-red-700 bg-red-50 p-2 rounded">
                                                                <p class="font-semibold">Потребни предуслови:</p>
                                                                <ul class="list-disc list-inside">
                                                                    @foreach($item['prerequisites'] as $prereq)
                                                                        <li class="{{ in_array($prereq->id, $completed->toArray()) ? 'line-through text-gray-500' : '' }}">{{ $prereq->code }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-amber-50 rounded-lg p-6 border-l-4 border-amber-600 opacity-60">
                                            <h5 class="font-bold text-lg text-amber-700 mb-2">☀️ Летни семестар</h5>
                                            <p class="text-gray-500 text-sm">Нема предмети</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 border-l-4 border-green-500 p-6 rounded">
                        <p class="text-green-900 font-semibold text-lg">
                            🎉 Честитки! Ги завршивте сите предмети за <strong>{{ $studyProgram->name_mk }}</strong>!
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="font-bold text-2xl text-gray-900 mb-8">Препорачани следни чекори</h3>

                @if(count($roadmap) > 0)
                    <div class="space-y-8">
                        @php
                            $readySubjects = array_filter($roadmap, fn($item) => $item['ready']);
                            $blockedSubjects = array_filter($roadmap, fn($item) => !$item['ready']);
                        @endphp

                        @if(count($readySubjects) > 0)
                            <div>
                                <h4 class="font-bold text-xl text-green-700 mb-4">Подготвени за запишување</h4>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach($readySubjects as $item)
                                        <div class="border-2 border-green-400 rounded-lg p-4 bg-green-50 hover:shadow-lg transition-shadow">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <p class="font-bold text-gray-900 text-lg">{{ $item['subject']->code }}</p>
                                                    <p class="text-gray-700 font-semibold">{{ $item['subject']->name }}</p>
                                                    @if($item['subject']->name_mk && $item['subject']->name_mk !== $item['subject']->name)
                                                        <p class="text-gray-600 text-sm italic">{{ $item['subject']->name_mk }}</p>
                                                    @endif
                                                </div>
                                                <span class="bg-green-600 text-white text-xs px-3 py-1 rounded-full font-semibold whitespace-nowrap ml-2">Подготвено</span>
                                            </div>
                                            <div class="mt-3 flex gap-2 flex-wrap">
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                    Година {{ $item['subject']->year }}
                                                </span>
                                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">
                                                    {{ $item['subject']->credits ?? 6 }} ECTS
                                                </span>
                                                <span class="inline-block bg-{{ $item['subject']->subject_type === 'mandatory' ? 'red' : 'orange' }}-100 text-{{ $item['subject']->subject_type === 'mandatory' ? 'red' : 'orange' }}-800 text-xs px-2 py-1 rounded">
                                                    {{ $item['subject']->subject_type === 'mandatory' ? 'Задолжително' : 'Избирачко' }}
                                                </span>
                                            </div>
                                            @if($item['subject']->description)
                                                <p class="text-gray-600 text-sm mt-3">{{ $item['subject']->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(count($blockedSubjects) > 0)
                            <div>
                                <h4 class="font-bold text-xl text-gray-700 mb-4">Невозможни (потребни предуслови)</h4>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach($blockedSubjects as $item)
                                        <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50 opacity-75">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <p class="font-bold text-gray-900 text-lg">{{ $item['subject']->code }}</p>
                                                    <p class="text-gray-700 font-semibold">{{ $item['subject']->name }}</p>
                                                    @if($item['subject']->name_mk && $item['subject']->name_mk !== $item['subject']->name)
                                                        <p class="text-gray-600 text-sm italic">{{ $item['subject']->name_mk }}</p>
                                                    @endif
                                                </div>
                                                <span class="bg-gray-600 text-white text-xs px-3 py-1 rounded-full font-semibold whitespace-nowrap ml-2">Заблокирано</span>
                                            </div>

                                            @if($item['prerequisites']->isNotEmpty())
                                                <div class="mt-3 p-3 bg-red-50 rounded border border-red-200">
                                                    <p class="text-sm font-semibold text-red-900 mb-2">Потребни предуслови:</p>
                                                    <ul class="text-sm text-red-800 space-y-1">
                                                        @foreach($item['prerequisites'] as $prereq)
                                                            @php
                                                                $isCompleted = in_array($prereq->id, $completed->toArray());
                                                            @endphp
                                                            <li class="flex items-center">
                                                                <span class="mr-2">
                                                                    @if($isCompleted)
                                                                        <span class="text-green-600">✓</span>
                                                                    @else
                                                                        <span class="text-red-600">✗</span>
                                                                    @endif
                                                                </span>
                                                                <span class="{{ $isCompleted ? 'line-through text-gray-500' : '' }}">
                                                                    {{ $prereq->code }}
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <div class="mt-3 flex gap-2 flex-wrap">
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                    Година {{ $item['subject']->year }}
                                                </span>
                                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">
                                                    {{ $item['subject']->credits ?? 6 }} ECTS
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 border-l-4 border-green-500 p-6 rounded">
                        <p class="text-green-900 font-semibold text-lg">
                            🎉 Честитки! Ги завршивте сите предмети за <strong>{{ $studyProgram->name_mk }}</strong>!
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Completed & In Progress Summary -->
        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
            @if(count($completed) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="font-bold text-lg text-gray-900 mb-4">✓ Завршени предмети</h3>
                        <div class="space-y-2">
                            @foreach($completed as $subjectId)
                                @php
                                    $subject = \App\Models\Subject::find($subjectId);
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded border border-green-200">
                                    <span class="text-gray-700">
                                        <strong>{{ $subject->code }}</strong> - {{ $subject->name }}
                                    </span>
                                    <span class="text-green-600 font-bold text-lg">✓</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if(count($inProgress) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="font-bold text-lg text-gray-900 mb-4">Во процес</h3>
                        <div class="space-y-2">
                            @foreach($inProgress as $subjectId)
                                @php
                                    $subject = \App\Models\Subject::find($subjectId);
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded border border-yellow-200">
                                    <span class="text-gray-700">
                                        <strong>{{ $subject->code }}</strong> - {{ $subject->name }}
                                    </span>
                                    <span class="text-yellow-600 font-bold text-lg">⏳</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
