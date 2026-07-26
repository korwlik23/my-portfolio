@extends('layouts.app')

@section('page-title', __('messages.add_user'))

@section('content')
<div class="max-w-xl">
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium">{{ __('messages.name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium">{{ __('messages.email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium">{{ __('messages.birth_date') }}</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium">{{ __('messages.password') }}</label>
                <input type="password" name="password" required class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium">{{ __('messages.role') }}</label>
                <select name="role" required class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.add_user') }}</button>
                <a href="{{ route('admin.users') }}" class="rounded-lg border border-zinc-200 px-5 py-2.5 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
