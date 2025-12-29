<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-bold text-2xl text-gray-900">Управување со студиски програми</h2>
                    <a href="{{ route('study-programs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + Додади нова програма
                    </a>
                </div>

                <div class="mb-6">
                    <form action="{{ route('study-programs.index') }}" method="GET" class="flex gap-3">
                        <input
                            type="text"
                            name="search"
                            placeholder="Пребарај по име или код..."
                            value="{{ request('search') }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        >
                        <button
                            type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md font-semibold text-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Пребарај
                        </button>
                        @if(request('search'))
                            <a
                                href="{{ route('study-programs.index') }}"
                                class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold text-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                            >
                                Ресетирај
                            </a>
                        @endif
                    </form>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6">
                    @forelse($studyPrograms as $program)
                        <div class="border border-gray-300 rounded-lg p-6 hover:shadow-lg transition">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $program->name_mk }}</h3>
                                    <p class="text-sm text-gray-600">{{ $program->name_en }} ({{ $program->code }})</p>
                                    <p class="text-sm text-gray-500 mt-2">{{ $program->duration_years }} години • {{ ucfirst($program->cycle) }} циклус</p>
                                </div>
                                <div class="space-x-2">
                                    <a href="{{ route('study-programs.show', $program) }}" class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold hover:bg-blue-200">Преглед</a>
                                    <a href="{{ route('study-programs.edit', $program) }}" class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold hover:bg-yellow-200">Уреди</a>
                                    <form method="POST" action="{{ route('study-programs.destroy', $program) }}" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')" class="px-3 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold hover:bg-red-200">Избриши</button>
                                    </form>
                                </div>
                            </div>
                            <div class="mb-3">
                                <p class="text-sm text-gray-700"><strong>Предмети:</strong> {{ $program->subjects->count() }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($program->subjects->take(5) as $subject)
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $subject->pivot->type === 'mandatory' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $subject->code }}
                                        </span>
                                    @endforeach
                                    @if($program->subjects->count() > 5)
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                            +{{ $program->subjects->count() - 5 }} повеќе
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($program->description_mk)
                                <p class="text-sm text-gray-600 italic">{{ \Illuminate\Support\Str::limit($program->description_mk, 150) }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500">Нема студиски програми</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $studyPrograms->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
