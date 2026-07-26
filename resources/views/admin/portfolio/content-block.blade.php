@extends('layouts.app')

@php
    $settings = $block->settings ?? [];
    $statsText = collect($settings['stats'] ?? [])->map(fn ($stat) => ($stat['value'] ?? '') . ' | ' . ($stat['label_en'] ?? '') . ' | ' . ($stat['label_th'] ?? ''))->implode("\n");
    $defaultQuickFactsEn = [
        ['label' => 'Location', 'value' => 'Thailand'],
        ['label' => 'Role', 'value' => 'Fullstack Developer'],
        ['label' => 'Core Stack', 'value' => 'Laravel / Next.js / MySQL'],
        ['label' => 'Focus', 'value' => 'Admin Systems / SaaS / Deployment'],
        ['label' => 'Available', 'value' => 'Full-time & Freelance'],
    ];
    $defaultQuickFactsTh = [
        ['label' => 'ที่อยู่', 'value' => 'ประเทศไทย'],
        ['label' => 'บทบาท', 'value' => 'Fullstack Developer'],
        ['label' => 'Core Stack', 'value' => 'Laravel / Next.js / MySQL'],
        ['label' => 'โฟกัส', 'value' => 'Admin Systems / SaaS / Deployment'],
        ['label' => 'รับงาน', 'value' => 'Full-time & Freelance'],
    ];
    $quickFactsTextEn = collect($settings['quick_facts_en'] ?? $defaultQuickFactsEn)->map(fn ($fact) => ($fact['label'] ?? '') . ' | ' . ($fact['value'] ?? ''))->implode("\n");
    $quickFactsTextTh = collect($settings['quick_facts_th'] ?? $defaultQuickFactsTh)->map(fn ($fact) => ($fact['label'] ?? '') . ' | ' . ($fact['value'] ?? ''))->implode("\n");
@endphp

@section('page-title', __('messages.' . $blockKey . '_content'))

@section('content')
<form method="POST" action="{{ route('admin.portfolio.content.update', $blockKey) }}" enctype="multipart/form-data" class="mx-auto max-w-5xl space-y-6">
    @csrf
    @method('PUT')

    <section class="grid gap-5 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 lg:grid-cols-2">
        <label class="block">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.title_th') }}</span>
            <input name="title_th" value="{{ old('title_th', $block->title_th) }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.title_en') }}</span>
            <input name="title_en" value="{{ old('title_en', $block->title_en) }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
        </label>
        <label class="block lg:col-span-2">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.subtitle_th') }}</span>
            <textarea name="subtitle_th" rows="3" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('subtitle_th', $block->subtitle_th) }}</textarea>
        </label>
        <label class="block lg:col-span-2">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.subtitle_en') }}</span>
            <textarea name="subtitle_en" rows="3" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('subtitle_en', $block->subtitle_en) }}</textarea>
        </label>
        <label class="block lg:col-span-2">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.body_th') }}</span>
            <textarea name="body_th" rows="5" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('body_th', $block->body_th) }}</textarea>
        </label>
        <label class="block lg:col-span-2">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.body_en') }}</span>
            <textarea name="body_en" rows="5" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('body_en', $block->body_en) }}</textarea>
        </label>
    </section>

    @if($blockKey === 'hero')
        <section class="grid gap-5 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 lg:grid-cols-2">
            @foreach(['primary_cta_text_th', 'primary_cta_text_en', 'primary_cta_url', 'secondary_cta_text_th', 'secondary_cta_text_en', 'secondary_cta_url'] as $field)
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium">{{ __('messages.' . $field) }}</span>
                    <input name="{{ $field }}" value="{{ old($field, $block->{$field}) }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </label>
            @endforeach
            <label class="block lg:col-span-2">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.availability_status') }}</span>
                <input name="availability" value="{{ old('availability', $settings['availability'] ?? '') }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </label>
            <label class="block lg:col-span-2">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.hero_stats') }}</span>
                <textarea name="stats" rows="4" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800" placeholder="Value | Label EN | Label TH">{{ old('stats', $statsText) }}</textarea>
            </label>
        </section>
    @endif

    @if($blockKey === 'about')
        <section class="grid gap-5 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.profile_image') }}</span>
                <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                @if($block->image_path)
                    <a href="{{ route('portfolio.media', ['path' => $block->image_path]) }}" target="_blank" class="mt-2 inline-block text-xs font-semibold text-blue-600 hover:underline">{{ __('messages.current_file') }}</a>
                @endif
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.years_experience') }}</span>
                <input name="years_experience" value="{{ old('years_experience', $settings['years_experience'] ?? '') }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.highlight_points_th') }}</span>
                <textarea name="highlights_th" rows="4" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('highlights_th', implode("\n", $settings['highlights_th'] ?? [])) }}</textarea>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.highlight_points_en') }}</span>
                <textarea name="highlights_en" rows="4" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('highlights_en', implode("\n", $settings['highlights_en'] ?? [])) }}</textarea>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.quick_facts_th') }}</span>
                <textarea name="quick_facts_th" rows="5" placeholder="Label | Value" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('quick_facts_th', $quickFactsTextTh) }}</textarea>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.quick_facts_en') }}</span>
                <textarea name="quick_facts_en" rows="5" placeholder="Label | Value" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('quick_facts_en', $quickFactsTextEn) }}</textarea>
            </label>
        </section>
    @endif

    @if($blockKey === 'deployment')
        <section class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.deployment_steps') }}</span>
                <textarea name="deployment_steps" rows="5" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old('deployment_steps', implode("\n", $settings['steps'] ?? [])) }}</textarea>
            </label>
        </section>
    @endif

    <section class="flex flex-wrap items-center justify-between gap-4 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <label class="flex items-center gap-3 text-sm font-medium">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $block->is_active))>
            {{ __('messages.active') }}
        </label>
        <label class="block">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.display_order') }}</span>
            <input type="number" name="display_order" value="{{ old('display_order', $block->display_order) }}" class="w-32 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
        </label>
        <button class="rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.save') }}</button>
    </section>
</form>
@endsection
