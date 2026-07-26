@extends('layouts.app')

@section('page-title', __('messages.site_settings'))

@section('content')
<form method="POST" action="{{ route('admin.portfolio.settings.update') }}" enctype="multipart/form-data" class="grid max-w-5xl gap-6 lg:grid-cols-2">
    @csrf
    @method('PUT')

    <section class="space-y-4 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <h2 class="font-semibold">{{ __('messages.site_settings') }}</h2>
        <label class="block">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.site_name') }}</span>
            <input name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.default_language') }}</span>
            <select name="default_language" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                <option value="en" @selected(old('default_language', $settings['default_language'] ?? 'en') === 'en')>EN</option>
                <option value="th" @selected(old('default_language', $settings['default_language'] ?? 'en') === 'th')>TH</option>
            </select>
        </label>
        <label class="block">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.logo') }}</span>
            <input type="file" name="logo" accept="image/*" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.favicon') }}</span>
            <input type="file" name="favicon" accept="image/*" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
        </label>
        <label class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
            <span class="text-sm font-medium">{{ __('messages.maintenance_mode') }}</span>
            <input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $settings['maintenance_mode'] ?? '0') === '1')>
        </label>
    </section>

    <section class="space-y-4 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <h2 class="font-semibold">{{ __('messages.contact_links') }}</h2>
        @foreach(['contact_email', 'phone_number', 'github_url', 'linkedin_url', 'facebook_url', 'discord_url', 'line_url', 'resume_url'] as $field)
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.' . $field) }}</span>
                <input name="{{ $field }}" value="{{ old($field, $settings[$field] ?? '') }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </label>
        @endforeach
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.resume_th_file') }}</span>
                <input type="file" name="resume_th" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                @if(!empty($settings['resume_th_path']))
                    <a href="{{ route('resume.download', 'th') }}" target="_blank" class="mt-2 inline-block text-xs font-semibold text-blue-600 hover:underline">{{ __('messages.current_file') }}</a>
                @endif
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.resume_en_file') }}</span>
                <input type="file" name="resume_en" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                @if(!empty($settings['resume_en_path']))
                    <a href="{{ route('resume.download', 'en') }}" target="_blank" class="mt-2 inline-block text-xs font-semibold text-blue-600 hover:underline">{{ __('messages.current_file') }}</a>
                @endif
            </label>
        </div>
        <button class="w-full rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.save') }}</button>
    </section>
</form>
@endsection
