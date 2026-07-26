@extends('layouts.app')

@section('page-title', __('messages.translations'))

@section('content')
<div class="mx-auto max-w-7xl">
    <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mb-7 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-xl font-bold tracking-tight">{{ __('messages.language_overrides') }}</h2>
                <form method="GET" action="{{ route('admin.translations.index') }}" class="mt-4 flex max-w-xl gap-2">
                    <input type="hidden" name="locale" value="{{ $selectedLocale }}">
                    <label class="sr-only" for="translation-search">{{ __('messages.search') }}</label>
                    <input id="translation-search" name="search" value="{{ $search }}" class="min-w-0 flex-1 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" placeholder="dashboard, login, settings">
                    <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">{{ __('messages.filter') }}</button>
                </form>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach($locales as $locale)
                    <a href="{{ route('admin.translations.index', ['locale' => $locale, 'search' => $search]) }}" class="rounded-xl border px-4 py-2 text-sm font-bold transition {{ $selectedLocale === $locale ? 'border-zinc-900 bg-zinc-900 text-white dark:border-white dark:bg-white dark:text-zinc-950' : 'border-zinc-200 text-zinc-500 hover:border-zinc-400 hover:text-zinc-900 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-zinc-500 dark:hover:text-white' }}">
                        {{ strtoupper($locale) }}
                    </a>
                @endforeach
            </div>
        </div>

        <details class="mb-5 rounded-xl border border-dashed border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
            <summary class="cursor-pointer text-sm font-semibold">{{ __('messages.add_custom_translation') }}</summary>
            <form method="POST" action="{{ route('admin.translations.store') }}" class="mt-4 grid gap-4 lg:grid-cols-[140px_220px_minmax(0,1fr)_auto]">
                @csrf
                <input type="hidden" name="locale" value="{{ $selectedLocale }}">
                <input type="hidden" name="group" value="messages">

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold text-zinc-500">{{ __('messages.language') }}</span>
                    <input value="{{ strtoupper($selectedLocale) }}" disabled class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold text-zinc-500">{{ __('messages.translation_key') }}</span>
                    <input name="key" value="{{ old('key') }}" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" placeholder="starter_hero_title">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold text-zinc-500">{{ __('messages.translation_value') }}</span>
                    <input name="value" value="{{ old('value') }}" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                </label>

                <button class="self-end rounded-lg bg-zinc-900 px-4 py-2 text-sm font-bold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                    {{ __('messages.save') }}
                </button>
            </form>
        </details>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase text-zinc-500">
                        <th class="w-[29%] px-4 py-3">{{ __('messages.translation_key') }}</th>
                        <th class="w-[40%] px-4 py-3">{{ __('messages.original_file') }}</th>
                        <th class="px-4 py-3">{{ __('messages.override_database') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($translations as $translation)
                        <tr class="{{ $loop->first ? 'bg-zinc-50 dark:bg-zinc-800/35' : '' }}">
                            <td class="px-4 py-4 align-middle">
                                <code class="break-all text-xs font-medium text-zinc-700 dark:text-zinc-200">{{ $translation->key }}</code>
                                @if($translation->is_overridden)
                                    <span class="ml-2 rounded bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ __('messages.overridden') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-middle text-zinc-500">
                                <div class="line-clamp-2 whitespace-pre-line">{{ $translation->base_value }}</div>
                            </td>
                            <td class="px-4 py-4 align-middle">
                                <form method="POST" action="{{ route('admin.translations.store') }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="locale" value="{{ $translation->locale }}">
                                    <input type="hidden" name="group" value="{{ $translation->group }}">
                                    <input type="hidden" name="key" value="{{ $translation->key }}">
                                    <label class="sr-only" for="translation-{{ $translation->locale }}-{{ md5($translation->group . '.' . $translation->key) }}">{{ __('messages.translation_value') }}</label>
                                    <input id="translation-{{ $translation->locale }}-{{ md5($translation->group . '.' . $translation->key) }}" name="value" value="{{ $translation->is_overridden ? $translation->value : '' }}" class="min-w-0 flex-1 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-semibold dark:border-zinc-700 dark:bg-zinc-800" placeholder="{{ __('messages.new_value') }}">
                                    <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-bold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                                        {{ __('messages.save') }}
                                    </button>
                                </form>
                                @if($translation->override)
                                    <form method="POST" action="{{ route('admin.translations.destroy', $translation->override) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-medium text-zinc-500 hover:text-red-600">{{ __('messages.reset') }}</button>
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

        <div class="mt-5">
            {{ $translations->links() }}
        </div>
    </section>
</div>
@endsection
