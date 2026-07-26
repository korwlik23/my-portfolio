@extends('layouts.app')

@php
    $editing = $seo->exists;
    $action = $editing ? route('admin.portfolio.seo.update', $seo) : route('admin.portfolio.seo.store');
@endphp

@section('page-title', ($editing ? __('messages.edit') : __('messages.create')) . ' ' . __('messages.seo_settings'))

@section('content')
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mx-auto max-w-5xl space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <section class="grid gap-5 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 lg:grid-cols-2">
        @foreach(['page_key', 'meta_title_th', 'meta_title_en', 'canonical_url', 'keywords'] as $field)
            <label class="block {{ $field === 'keywords' ? 'lg:col-span-2' : '' }}">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.' . $field) }}</span>
                <input name="{{ $field }}" value="{{ old($field, $seo->{$field}) }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            </label>
        @endforeach
        @foreach(['meta_description_th', 'meta_description_en'] as $field)
            <label class="block lg:col-span-2">
                <span class="mb-1.5 block text-sm font-medium">{{ __('messages.' . $field) }}</span>
                <textarea name="{{ $field }}" rows="4" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ old($field, $seo->{$field}) }}</textarea>
            </label>
        @endforeach
        <label class="block">
            <span class="mb-1.5 block text-sm font-medium">{{ __('messages.og_image') }}</span>
            <input type="file" name="og_image" accept="image/*" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
        </label>
    </section>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.portfolio.seo.index') }}" class="rounded-lg px-5 py-2.5 text-sm font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800">{{ __('messages.cancel') }}</a>
        <button class="rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.save') }}</button>
    </div>
</form>
@endsection
