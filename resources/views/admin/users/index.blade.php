@extends('layouts.app')

@section('page-title', __('messages.all_users'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end gap-3">
        <form class="flex flex-wrap items-end gap-2">
            <div>
                <label class="mb-1 block text-xs font-medium">{{ __('messages.role') }}</label>
                <select name="role" class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium">{{ __('messages.status') }}</label>
                <select name="status" class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach(['active', 'inactive', 'banned'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('messages.' . $status) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white dark:bg-white dark:text-zinc-900">{{ __('messages.filter') }}</button>
        </form>

        <a href="{{ route('admin.users.create') }}" class="ml-auto rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
            {{ __('messages.add_user') }}
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-800">
                        <th class="px-5 py-3">{{ __('messages.name') }}</th>
                        <th class="px-5 py-3">{{ __('messages.email') }}</th>
                        <th class="px-5 py-3">{{ __('messages.roles') }}</th>
                        <th class="px-5 py-3">{{ __('messages.status') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('messages.manage') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-5 py-3 text-zinc-500">{{ $user->email }}</td>
                            <td class="px-5 py-3">
                                @foreach($user->getRoleNames() as $role)
                                    <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs dark:bg-zinc-800">{{ $role }}</span>
                                @endforeach
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs {{ $user->status->value === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ __('messages.' . $user->status->value) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                        {{ __('messages.edit') }}
                                    </a>
                                    @if($user->status->value === 'active')
                                        <button type="button" x-data @click="$dispatch('open-ban-modal', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })" @disabled($user->id === auth()->id()) class="rounded-md px-2 py-1 text-xs font-medium text-orange-500 hover:bg-orange-50 hover:text-orange-700 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent dark:hover:bg-orange-950/30">
                                            {{ __('messages.ban_user') }}
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline" onsubmit="return confirm('{{ __('messages.unban_user') }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button @disabled($user->id === auth()->id()) class="rounded-md px-2 py-1 text-xs font-medium text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent dark:hover:bg-emerald-950/30">
                                                {{ __('messages.unban_user') }}
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" x-data @click="$dispatch('open-reset-password', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })" class="rounded-md px-2 py-1 text-xs font-medium text-indigo-500 hover:bg-indigo-50 hover:text-indigo-700 dark:hover:bg-indigo-950/30">
                                        {{ __('messages.reset_password') }}
                                    </button>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('{{ __('messages.confirm_delete_user') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button @disabled($user->id === auth()->id()) class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium text-red-500 hover:bg-red-50 hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent dark:hover:bg-red-950/30">
                                            <x-icon name="delete" class="h-3.5 w-3.5" />
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-zinc-500">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-800">{{ $users->links() }}</div>
        @endif
    </div>
</div>

<div x-data="{ open: false, userId: null, userName: '' }"
     @open-ban-modal.window="open = true; userId = $event.detail.id; userName = $event.detail.name"
     x-show="open"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     style="display: none;">
    <div @click.away="open = false" class="w-full max-w-md rounded-lg border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-800 dark:bg-zinc-900">
        <h3 class="mb-4 text-lg font-bold">{{ __('messages.ban_user') }}: <span x-text="userName"></span></h3>
        <form :action="'/admin/users/' + userId + '/ban'" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('messages.ban_duration') }}</label>
                <select name="duration" required class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="15">15 {{ __('messages.minutes') }}</option>
                    <option value="60">60 {{ __('messages.minutes') }}</option>
                    <option value="1440">1 {{ __('messages.days') }}</option>
                    <option value="10080">7 {{ __('messages.days') }}</option>
                    <option value="permanent">{{ __('messages.permanent') }}</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('messages.reason') }}</label>
                <textarea name="reason" required rows="3" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="open = false" class="rounded-lg px-4 py-2 text-sm font-medium">{{ __('messages.cancel') }}</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600">{{ __('messages.ban_user') }}</button>
            </div>
        </form>
    </div>
</div>

<div x-data="{ open: false, userId: null, userName: '' }"
     @open-reset-password.window="open = true; userId = $event.detail.id; userName = $event.detail.name"
     x-show="open"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     style="display: none;">
    <div @click.away="open = false" class="w-full max-w-md rounded-lg border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-800 dark:bg-zinc-900">
        <h3 class="mb-4 text-lg font-bold">{{ __('messages.reset_password') }}: <span x-text="userName"></span></h3>
        <form :action="'/admin/users/' + userId + '/reset-password'" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('messages.new_password') }}</label>
                <input type="password" name="password" required class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('messages.confirm_password') }}</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="open = false" class="rounded-lg px-4 py-2 text-sm font-medium">{{ __('messages.cancel') }}</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ __('messages.reset_password') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
