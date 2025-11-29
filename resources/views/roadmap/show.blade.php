<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="font-bold text-3xl text-gray-900">Вашата Академска Дорога</h2>
                        <p class="text-gray-600 mt-2 text-lg">
                            <strong class="text-indigo-600">{{ $studyProgram->name_mk }}</strong>
                            <span class="text-gray-500">({{ $studyProgram->duration_years }} години)</span>
                        </p>
                        @if($studyProgram->name_en)
                            <p class="text-gray-500 italic">{{ $studyProgram->name_en }}</p>
                        @endif
                    </div>
                    <a href="{{ route('roadmap.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300">
                        Уреди дорога
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
                    <p class="text-3xl font-bold text-blue-600">{{ count($roadmap) }}</p>
                </div>
            </div>
            <div class="bg-purple-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-l-4 border-purple-500">
                    <p class="text-gray-500 text-sm">Вкупно ECTS</p>
                    @php
                        $totalEcts = 0;
                        foreach($studyProgram->subjects as $subject) {
                            $totalEcts += $subject->credits ?? 0;
                        }
                    @endphp
                    <p class="text-3xl font-bold text-purple-600">{{ $totalEcts }}</p>
                </div>
            </div>
        </div>

        <!-- Recommended Subjects -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="font-bold text-2xl text-gray-900 mb-8">Препоракушани следни чекори</h3>

                @if(count($roadmap) > 0)
                    <div class="space-y-8">
                        @php
                            $readySubjects = array_filter($roadmap, fn($item) => $item['ready']);
                            $blockedSubjects = array_filter($roadmap, fn($item) => !$item['ready']);
                        @endphp

                        @if(count($readySubjects) > 0)
                            <div>
                                <h4 class="font-bold text-xl text-green-700 mb-4">✓ Подготвени за запишување</h4>
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
                                <h4 class="font-bold text-xl text-gray-700 mb-4">🔒 Заблокирано (потребни предусловиsection)</h4>
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
                                                    <p class="text-sm font-semibold text-red-900 mb-2">Потребни предусловиsection:</p>
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
                        <h3 class="font-bold text-lg text-gray-900 mb-4">⏳ Во процес</h3>
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
