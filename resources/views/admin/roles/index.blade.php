@extends('layouts.app')

@section('page-title', __('messages.manage_system_roles'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-zinc-500">{{ $roles->count() }} {{ __('messages.roles') }}</p>
        <a href="{{ route('admin.roles.create') }}" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">{{ __('messages.add_new_role') }}</a>
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-800">
                    <th class="px-5 py-3">{{ __('messages.role_name') }}</th>
                    <th class="px-5 py-3">{{ __('messages.permissions') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('messages.manage') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($roles as $role)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $role->name }}</td>
                        <td class="px-5 py-3 text-zinc-500">{{ $role->permissions->count() }} {{ __('messages.permissions') }}</td>
                        <td class="space-x-2 px-5 py-3 text-right">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center gap-1.5 text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                                <x-icon name="edit" class="h-3.5 w-3.5" />
                                {{ __('messages.edit') }}
                            </a>
                            @if(!in_array($role->name, ['admin', 'user']))
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('{{ __('messages.confirm_delete_role') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700">
                                        <x-icon name="delete" class="h-3.5 w-3.5" />
                                        {{ __('messages.delete') }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-zinc-500">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
