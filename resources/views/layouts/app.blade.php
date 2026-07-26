@extends('layouts.base')

@section('body')
<div class="min-h-screen lg:flex">
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-zinc-200 bg-white transition-transform dark:border-zinc-800 dark:bg-zinc-900 lg:static lg:translate-x-0">
        <div class="flex h-16 items-center border-b border-zinc-200 px-6 dark:border-zinc-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                @if(!empty($globalSystemSettings['logo_path']))
                    <img src="{{ route('portfolio.media', ['path' => $globalSystemSettings['logo_path']]) }}" alt="{{ config('app.name') }}" class="h-8 w-8 rounded object-contain">
                @else
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 text-sm font-bold text-white dark:bg-white dark:text-zinc-900">LS</span>
                @endif
                <span class="font-semibold tracking-tight">{{ $globalSystemSettings['brand_name'] ?? config('app.name') }}</span>
            </a>
        </div>

        <nav class="flex-1 space-y-1 p-3">
            @php $current = request()->segment(1); @endphp
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $current === 'dashboard' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                <x-icon name="dashboard" class="h-4 w-4" />
                {{ __('messages.dashboard') }}
            </a>

            @can('admin.access')
                <div class="pt-4">
                    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('messages.management') }}</p>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/dashboard') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                        <x-icon name="dashboard" class="h-4 w-4" />
                        {{ __('messages.system_dashboard') }}
                    </a>
                    @can('portfolio.view')
                        <a href="{{ route('admin.portfolio.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/portfolio') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                            <x-icon name="dashboard" class="h-4 w-4" />
                            {{ __('messages.portfolio_cms') }}
                        </a>
                    @endcan
                    @can('portfolio.manage')
                        <a href="{{ route('admin.portfolio.settings') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/portfolio/settings') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                            <x-icon name="settings" class="h-4 w-4" />
                            {{ __('messages.site_settings') }}
                        </a>
                        <a href="{{ route('admin.portfolio.resources.index', 'projects') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/portfolio/projects*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                            <x-icon name="audit" class="h-4 w-4" />
                            {{ __('messages.projects') }}
                        </a>
                        <a href="{{ route('admin.portfolio.resources.index', 'starters') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/portfolio/starters*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                            <x-icon name="audit" class="h-4 w-4" />
                            {{ __('messages.starters') }}
                        </a>
                    @endcan
                    @can('user.manage')
                        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/users*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                            <x-icon name="users" class="h-4 w-4" />
                            {{ __('messages.users') }}
                        </a>
                    @endcan
                    @can('role.manage')
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/roles*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                            <x-icon name="roles" class="h-4 w-4" />
                            {{ __('messages.roles') }}
                        </a>
                    @endcan
                    @can('translation.manage')
                        <a href="{{ route('admin.translations.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/translations*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                            <x-icon name="translations" class="h-4 w-4" />
                            {{ __('messages.translations') }}
                        </a>
                    @endcan
                    @can('audit.view')
                        <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->is('admin/audit-logs*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                            <x-icon name="audit" class="h-4 w-4" />
                            {{ __('messages.audit_logs') }}
                        </a>
                    @endcan
                </div>
            @endcan

            <div class="pt-4">
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $current === 'settings' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                    <x-icon name="settings" class="h-4 w-4" />
                    {{ __('messages.settings') }}
                </a>
            </div>
        </nav>

        <div class="border-t border-zinc-200 p-3 dark:border-zinc-800">
            <a href="{{ route('profile') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition hover:bg-zinc-100 dark:hover:bg-zinc-800">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-sm font-bold text-white dark:bg-white dark:text-zinc-900">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </span>
                <span class="min-w-0">
                    <span class="block truncate font-semibold">{{ auth()->user()->name }}</span>
                    <span class="block truncate text-xs text-zinc-500">{{ auth()->user()->email }}</span>
                </span>
            </a>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-zinc-200 bg-white/90 px-4 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90 lg:px-6">
            <div class="flex items-center gap-3">
                <button type="button" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="rounded-lg p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 lg:hidden" aria-label="Toggle navigation">
                    <span class="block h-0.5 w-5 bg-current"></span>
                    <span class="mt-1 block h-0.5 w-5 bg-current"></span>
                    <span class="mt-1 block h-0.5 w-5 bg-current"></span>
                </button>
                <h1 class="text-base font-semibold">@yield('page-title', config('app.name'))</h1>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('profile') }}" class="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800 sm:inline-flex">
                    <x-icon name="profile" class="h-4 w-4" />
                    {{ __('messages.profile') }}
                </a>
                <form method="GET" action="{{ route('lang.switch', app()->getLocale()) }}">
                    <label class="sr-only" for="topbar-locale">{{ __('messages.language') }}</label>
                    <select id="topbar-locale" onchange="window.location.href='{{ url('/lang') }}/' + this.value" class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-bold dark:border-zinc-700 dark:bg-zinc-950">
                        @foreach(['th', 'en'] as $locale)
                            <option value="{{ $locale }}" @selected(app()->getLocale() === $locale)>{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                </form>
                <button type="button" onclick="toggleLargeText()" id="large-text-toggle" class="rounded-lg px-3 py-2 text-sm font-bold hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-pressed="false" title="{{ __('messages.large_text_mode') }}">
                    Aa
                </button>
                <button type="button" onclick="toggleDarkMode()" class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="{{ __('messages.toggle_mode') }}" title="{{ __('messages.toggle_mode') }}">
                    <x-icon name="sun" class="h-5 w-5" />
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="{{ __('messages.logout') }}" title="{{ __('messages.logout') }}">
                        <x-icon name="logout" class="h-5 w-5" />
                    </button>
                </form>
            </div>
        </header>

        <main class="p-4 lg:p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@endsection
