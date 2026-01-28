<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="font-bold text-3xl text-gray-900">Вашиот Академски Roadmap</h2>
            </div>
        </div>

        <!-- Empty State -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-6 rounded">
            <p class="text-blue-900 font-semibold text-lg mb-3">
                📋 Немаш создадено roadmap.
            </p>
            <p class="text-blue-800 mb-4">Почни со создавање на твој прв академски roadmap за да го планираш твоето студирање.</p>
            <a href="{{ route('roadmap.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Создај Roadmap
            </a>
        </div>
    </div>
</div>
</x-app-layout>
