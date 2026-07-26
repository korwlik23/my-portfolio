@extends('layouts.app')

@section('page-title', __('messages.dashboard'))

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">{{ __('messages.dashboard') }}</h2>
        <p class="mt-1 text-sm text-zinc-500">{{ __('messages.starter_dashboard_intro') }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('messages.users') }}</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($stats['users']) }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('messages.roles') }}</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($stats['roles']) }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('messages.permissions') }}</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($stats['permissions']) }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('messages.starter_queue_status') }}</p>
            <p class="mt-2 text-lg font-semibold {{ $stats['queue_ready'] ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ $stats['queue_ready'] ? __('messages.active') : __('messages.not_specified') }}
            </p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <a href="{{ route('settings.index') }}" class="rounded-lg border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
            <h3 class="font-semibold">{{ __('messages.settings') }}</h3>
            <p class="mt-2 text-sm leading-6 text-zinc-500">{{ __('messages.starter_settings_card') }}</p>
        </a>

        @can('user.manage')
            <a href="{{ route('admin.users') }}" class="rounded-lg border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                <h3 class="font-semibold">{{ __('messages.users') }}</h3>
                <p class="mt-2 text-sm leading-6 text-zinc-500">{{ __('messages.starter_users_card') }}</p>
            </a>
        @endcan

        @can('role.manage')
            <a href="{{ route('admin.roles.index') }}" class="rounded-lg border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                <h3 class="font-semibold">{{ __('messages.roles') }}</h3>
                <p class="mt-2 text-sm leading-6 text-zinc-500">{{ __('messages.starter_roles_card') }}</p>
            </a>
        @endcan
    </div>
</div>
@endsection
