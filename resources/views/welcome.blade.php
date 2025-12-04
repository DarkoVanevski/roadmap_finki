<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FINKI Roadmap</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-indigo-600">🎓 FINKI Roadmap</h1>
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="bg-emerald-700 text-white px-4 py-2 rounded-lg hover:bg-teal-700">Најава</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Регистрација</a>
                        @endif
                    @endauth
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Планирајте ја вашата академска иднина
                </h2>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    Добредојдовте во FINKI Roadmap, вашиот личен водич за академско совршенство.
                    Откријте како да изберете изборни предмети што одговараат на вашите кариерни аспирации и да ја оптимизирате вашата академска патека.
                </p>

                @auth
                    <a href="{{ route('roadmap.create') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-bold">
                        Создајте го вашиот roadmap
                    </a>
                @else
                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-bold">
                            Почнете
                        </a>
                        <a href="{{ route('login') }}" class="bg-white text-indigo-600 border-2 border-indigo-600 px-8 py-3 rounded-lg hover:bg-indigo-50 font-bold">
                            Најава
                        </a>
                    </div>
                @endauth
            </div>
        </section>

        <!-- Features Section -->
        <section class="bg-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="text-3xl font-bold text-center text-gray-900 mb-12">Зошто да го одберете FINKI Roadmap?</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-8 border border-gray-200 rounded-lg hover:shadow-lg transition">
                        <h4 class="text-xl font-bold text-indigo-600 mb-4">📚 Паметно планирање</h4>
                        <p class="text-gray-600">Следете го вашиот напредок и добијте интелигентни препораки за следното семестре.</p>
                    </div>

                    <div class="p-8 border border-gray-200 rounded-lg hover:shadow-lg transition">
                        <h4 class="text-xl font-bold text-indigo-600 mb-4">🎯 Избор на изборни предмети</h4>
                        <p class="text-gray-600">Одберете предмети што се вклопуваат со вашите интереси и будни каријерни цели.</p>
                    </div>

                    <div class="p-8 border border-gray-200 rounded-lg hover:shadow-lg transition">
                        <h4 class="text-xl font-bold text-indigo-600 mb-4">📊 Следење на напредокот</h4>
                        <p class="text-gray-600">Следете ги вашите ECTS кредити и останете информирани за барањата за дипломирање.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-indigo-600 text-white py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h3 class="text-3xl font-bold mb-6">Готови ли сте да ја планирате вашата академска патека?</h3>
                <p class="text-lg mb-8">Почнете да ја градите вашата персонализирана дорога денес и преземете контрола над вашата академска иднина.</p>

                @guest
                    <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg hover:bg-gray-100 font-bold">
                        Креирајте ваш профил сега (наскоро со CAS интеграција)
                    </a>
                @endguest
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-300 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; 2025 FINKI Roadmap. 214004 .</p>
            </div>
        </footer>
    </body>
</html>
