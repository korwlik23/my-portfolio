<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $globalSystemSettings['brand_name'] ?? config('app.name', 'Laravel Starter'))</title>
    <meta name="description" content="@yield('meta_description', __('messages.starter_app_desc'))">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|sarabun:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased">
    <nav class="sticky top-0 z-50 border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
        <div class="mx-auto flex min-h-16 max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6">
            <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-2.5">
                @if(!empty($globalSystemSettings['logo_path']))
                    <div class="flex h-8 w-8 items-center justify-center overflow-hidden">
                        <img src="{{ route('portfolio.media', ['path' => $globalSystemSettings['logo_path']]) }}" alt="Logo" class="h-full w-full object-contain">
                    </div>
                @else
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 dark:bg-white">
                        <span class="text-sm font-bold text-white dark:text-zinc-900">LS</span>
                    </div>
                @endif
                <span class="truncate text-lg font-semibold">{{ $globalSystemSettings['brand_name'] ?? config('app.name', 'Laravel Starter') }}</span>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <select onchange="window.location.href='/lang/'+this.value" class="h-10 rounded-lg border border-zinc-200 bg-transparent px-3 text-xs font-semibold text-zinc-700 focus:outline-none dark:border-zinc-700 dark:text-zinc-200">
                    @php
                        $locales = ['th' => 'TH', 'en' => 'EN'];
                        $current = session('locale', config('app.locale', 'th'));
                    @endphp
                    @foreach($locales as $code => $name)
                        <option value="{{ $code }}" class="text-zinc-900" {{ $current === $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>

                <button onclick="toggleDarkMode()" class="flex h-10 w-10 items-center justify-center rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" aria-label="Toggle dark mode">
                    <svg class="h-5 w-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="hidden h-5 w-5 dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                @yield('nav-actions')
            </div>
        </div>
    </nav>

    @yield('body')

    <footer class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto max-w-6xl px-6 py-12">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <div class="mb-4 flex items-center gap-2">
                        @if(!empty($globalSystemSettings['logo_path']))
                            <div class="flex h-7 w-7 items-center justify-center overflow-hidden">
                                <img src="{{ route('portfolio.media', ['path' => $globalSystemSettings['logo_path']]) }}" alt="Logo" class="h-full w-full object-contain">
                            </div>
                        @else
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-900 dark:bg-white">
                                <span class="text-xs font-bold text-white dark:text-zinc-900">LS</span>
                            </div>
                        @endif
                        <span class="font-semibold">{{ $globalSystemSettings['brand_name'] ?? config('app.name', 'Laravel Starter') }}</span>
                    </div>
                    <p class="text-sm leading-6 text-zinc-500 dark:text-zinc-400">{{ __('messages.landing_footer_desc') }}</p>
                </div>

                <div>
                    <h4 class="mb-3 text-sm font-semibold">{{ __('messages.landing_footer_links') }}</h4>
                    <ul class="space-y-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <li><a href="{{ route('legal.privacy') }}" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('messages.privacy_policy') }}</a></li>
                        <li><a href="{{ route('legal.terms') }}" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('messages.terms_of_service') }}</a></li>
                        <li><a href="{{ route('legal.refund') }}" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('messages.refund_policy') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-3 text-sm font-semibold">{{ __('messages.landing_footer_contact') }}</h4>
                    <ul class="space-y-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <li>{{ __('messages.landing_contact_email') }}: support@example.com</li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 border-t border-zinc-200 pt-6 text-center text-xs text-zinc-400 dark:border-zinc-800">
                &copy; {{ date('Y') }} {{ $globalSystemSettings['brand_name'] ?? config('app.name', 'Laravel Starter') }}. {{ __('messages.all_rights_reserved') }}
            </div>
        </div>
    </footer>

    <script>
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }
    </script>
    @include('components.page-loader')
    @stack('scripts')
</body>
</html>
