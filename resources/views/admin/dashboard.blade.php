@extends('layouts.app')

@section('page-title', __('messages.system_dashboard'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('messages.total_users') }}</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($stats['total_users']) }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('messages.active') }}</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">{{ number_format($stats['active_users']) }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('messages.banned') }}</p>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($stats['banned_users']) }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('messages.roles') }}</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($stats['total_roles']) }}</p>
        </div>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
            <h3 class="font-semibold">{{ __('messages.starter_recent_users') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-800">
                        <th class="px-5 py-3">{{ __('messages.name') }}</th>
                        <th class="px-5 py-3">{{ __('messages.email') }}</th>
                        <th class="px-5 py-3">{{ __('messages.roles') }}</th>
                        <th class="px-5 py-3">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($recentUsers as $user)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-5 py-3 text-zinc-500">{{ $user->email }}</td>
                            <td class="px-5 py-3">
                                @foreach($user->getRoleNames() as $role)
                                    <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs dark:bg-zinc-800">{{ $role }}</span>
                                @endforeach
                            </td>
                            <td class="px-5 py-3">{{ $user->status->value }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-zinc-500">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
