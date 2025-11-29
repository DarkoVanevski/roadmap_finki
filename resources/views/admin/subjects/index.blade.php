<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-bold text-2xl text-gray-900">Управување со предмети</h2>
                    <a href="{{ route('subjects.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + Додади нов предмет
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold text-gray-900">Код</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold text-gray-900">Име (EN)</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold text-gray-900">Име (MK)</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold text-gray-900">Година</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold text-gray-900">Семестар</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold text-gray-900">Тип</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold text-gray-900">ECTS</th>
                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-900">Акции</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjects as $subject)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900 font-mono">{{ $subject->code }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">{{ $subject->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-600">{{ $subject->name_mk }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900 text-center">{{ $subject->year }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $subject->semester_type === 'winter' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $subject->semester_type === 'winter' ? 'Зимски' : 'Летен' }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $subject->subject_type === 'mandatory' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $subject->subject_type === 'mandatory' ? 'Задолж.' : 'Изборен' }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900 text-center">{{ $subject->credits }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-center space-x-2">
                                        <a href="{{ route('subjects.show', $subject) }}" class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold hover:bg-blue-200">Преглед</a>
                                        <a href="{{ route('subjects.edit', $subject) }}" class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold hover:bg-yellow-200">Уреди</a>
                                        <form method="POST" action="{{ route('subjects.destroy', $subject) }}" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')" class="px-3 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold hover:bg-red-200">Избриши</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="border border-gray-300 px-4 py-4 text-center text-gray-500">Нема предмети</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $subjects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
