@extends('layouts.app')

@section('page-title', __('messages.settings'))

@section('content')
<div class="grid max-w-6xl gap-6 lg:grid-cols-2">
    <div class="space-y-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 font-semibold">{{ __('messages.profile') }}</h3>
            <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1.5 block text-sm font-medium">{{ __('messages.name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">{{ __('messages.birth_date') }}</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </div>
                <button class="rounded-lg bg-zinc-900 px-5 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.save') }}</button>
            </form>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 font-semibold">{{ __('messages.change_password') }}</h3>
            <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1.5 block text-sm font-medium">{{ __('messages.current_password') }}</label>
                    <input type="password" name="current_password" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">{{ __('messages.new_password') }}</label>
                    <input type="password" name="password" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">{{ __('messages.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </div>
                <button class="rounded-lg bg-zinc-900 px-5 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.change_password') }}</button>
            </form>
        </div>
    </div>

    @can('settings.manage')
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 font-semibold">{{ __('messages.system_settings') }}</h3>
            <form method="POST" action="{{ route('settings.system') }}" class="space-y-5" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1.5 block text-sm font-medium">{{ __('messages.platform_brand_name') }}</label>
                    <input type="text" name="brand_name" value="{{ old('brand_name', $systemSettings['brand_name'] ?? '') }}" placeholder="{{ config('app.name') }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">{{ __('messages.platform_logo') }}</label>
                    <input type="file" name="logo" accept="image/*" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </div>
                <label class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <span>
                        <span class="block text-sm font-medium">{{ __('messages.require_email_verification') }}</span>
                        <span class="mt-1 block text-xs text-zinc-500">{{ __('messages.require_email_verification_desc') }}</span>
                    </span>
                    <input type="checkbox" name="require_email_verification" class="h-4 w-4" {{ ($systemSettings['require_email_verification'] ?? true) ? 'checked' : '' }}>
                </label>
                <label class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <span>
                        <span class="block text-sm font-medium">{{ __('messages.bypass_password_validation') }}</span>
                        <span class="mt-1 block text-xs text-zinc-500">{{ __('messages.bypass_password_validation_desc') }}</span>
                    </span>
                    <input type="checkbox" name="bypass_password_validation" class="h-4 w-4" {{ ($systemSettings['bypass_password_validation'] ?? false) ? 'checked' : '' }}>
                </label>
                <button type="submit" class="w-full rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.save') }}</button>
            </form>
        </div>
    @endcan
</div>
@endsection
