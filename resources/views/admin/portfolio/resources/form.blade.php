@extends('layouts.app')

@php
    $editing = $item->exists;
    $action = $editing
        ? route('admin.portfolio.resources.update', [$resource, $item->id])
        : route('admin.portfolio.resources.store', $resource);

    $textareaFields = ['description_th', 'description_en', 'case_study_th', 'case_study_en', 'setup_notes_th', 'setup_notes_en', 'deploy_notes_th', 'deploy_notes_en', 'feature_list_th', 'feature_list_en', 'stack', 'tech_stack', 'tech_stack_tags'];
    $checkboxFields = ['is_active', 'is_public', 'is_featured'];
    $selectOptions = [
        'category' => ['Backend', 'Frontend', 'Database', 'DevOps', 'Product', 'Tools', 'AI'],
        'level' => ['basic', 'intermediate', 'advanced'],
    ];
    $selectOptions = array_replace($selectOptions, $config['select_options'] ?? []);
@endphp

@section('page-title', ($editing ? __('messages.edit') : __('messages.create')) . ' ' . $config['title'])

@section('content')
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mx-auto max-w-5xl space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/30 dark:text-red-300">
            {{ __('messages.validation_failed') }}
        </div>
    @endif

    <section class="grid gap-5 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 lg:grid-cols-2">
        @foreach($config['fields'] as $field)
            @php
                $value = old($field, $extra[$field] ?? $item->{$field} ?? '');
                $label = __('messages.' . $field);
            @endphp

            @if(in_array($field, $checkboxFields, true))
                <label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-4 text-sm font-medium dark:border-zinc-800">
                    <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $item->{$field} ?? true))>
                    {{ $label }}
                </label>
            @elseif($field === 'image')
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium">{{ __('messages.image') }}</span>
                    <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </label>
            @elseif(isset($selectOptions[$field]))
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium">{{ $label }}</span>
                    <select name="{{ $field }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                        <option value="">-</option>
                        @foreach($selectOptions[$field] as $optionValue => $optionLabel)
                            @php
                                $actualValue = is_int($optionValue) ? $optionLabel : $optionValue;
                                $actualLabel = is_int($optionValue) ? str($optionLabel)->replace('-', ' ')->headline()->toString() : $optionLabel;
                            @endphp
                            <option value="{{ $actualValue }}" @selected((string) $value === (string) $actualValue)>{{ $actualLabel }}</option>
                        @endforeach
                    </select>
                </label>
            @elseif(in_array($field, $textareaFields, true))
                <label class="block lg:col-span-2">
                    <span class="mb-1.5 block text-sm font-medium">{{ $label }}</span>
                    <textarea name="{{ $field }}" rows="5" @if($field === 'tech_stack_tags') placeholder="Cloudflare | Supabase | Svelte | Vite" @endif class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">{{ $value }}</textarea>
                </label>
            @else
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium">{{ $label }}</span>
                    <input name="{{ $field }}" value="{{ $value }}" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                </label>
            @endif
        @endforeach
    </section>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.portfolio.resources.index', $resource) }}" class="rounded-lg px-5 py-2.5 text-sm font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800">{{ __('messages.cancel') }}</a>
        <button class="rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.save') }}</button>
    </div>
</form>
@endsection
