<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Контролна табла') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Welcome Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">Добредојде, {{ Auth::user()->name }}!</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            @if(Auth::user()->isAdmin())
                                Администраторски кориснички сметка. Управувајте со предметите и системските подесувања.
                            @else
                                Планирајте ја вашата академска патека и добијте персонализиран roadmap врз основа на вашите академски цели.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Roadmap Card (for students) -->
                @if(Auth::user()->isStudent())
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100 mb-2">Вашиот Академски roadmap</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">
                                Создајте персонализиран roadmap според избраната студиска програма.
                            </p>
                            <a href="{{ route('roadmap.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Почнете
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Admin Panel Card (for admins) -->
                @if(Auth::user()->isAdmin())
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100 mb-2">Управување со Предмети</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">
                                Додајте, уредувајте и бришите предмети. Управувајте со предуслови.
                            </p>
                            <a href="{{ route('subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Предмети
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
