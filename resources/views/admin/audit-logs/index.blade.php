@extends('layouts.app')

@section('page-title', __('messages.audit_logs'))

@section('content')
<div class="space-y-6">
    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid gap-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 md:grid-cols-[1fr_180px_220px_auto]">
        <label>
            <span class="mb-1 block text-xs font-semibold text-zinc-500">{{ __('messages.search') }}</span>
            <input name="search" value="{{ request('search') }}" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" placeholder="login, user, IP">
        </label>

        <label>
            <span class="mb-1 block text-xs font-semibold text-zinc-500">{{ __('messages.action') }}</span>
            <select name="action" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('messages.all') }}</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                @endforeach
            </select>
        </label>

        <label>
            <span class="mb-1 block text-xs font-semibold text-zinc-500">{{ __('messages.user') }}</span>
            <select name="user_id" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('messages.all') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </label>

        <button class="self-end rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white dark:bg-white dark:text-zinc-900">
            {{ __('messages.filter') }}
        </button>
    </form>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-800">
                    <th class="px-5 py-3">{{ __('messages.created_at') }}</th>
                    <th class="px-5 py-3">{{ __('messages.action') }}</th>
                    <th class="px-5 py-3">{{ __('messages.user') }}</th>
                    <th class="px-5 py-3">{{ __('messages.description') }}</th>
                    <th class="px-5 py-3">{{ __('messages.ip_address') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap px-5 py-3 text-zinc-500">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded bg-zinc-100 px-2 py-1 text-xs font-medium dark:bg-zinc-800">{{ $log->action }}</span>
                        </td>
                        <td class="px-5 py-3">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-5 py-3 text-zinc-600 dark:text-zinc-300">
                            <div>{{ $log->description }}</div>
                            @if($log->auditable_type)
                                <div class="mt-1 text-xs text-zinc-400">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-zinc-500">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-zinc-500">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
@endsection
