@extends('layouts.base')

@section('title', __('messages.maintenance_title'))
@section('meta_description', __('messages.maintenance_body'))

@section('body')
<main class="grid min-h-screen place-items-center bg-white px-4 py-16 text-zinc-950 dark:bg-zinc-950 dark:text-white">
    <section class="w-full max-w-2xl text-center">
        <p class="mx-auto mb-5 inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 dark:border-blue-900 dark:bg-blue-950/50 dark:text-blue-300">
            {{ __('messages.maintenance_badge') }}
        </p>
        <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">{{ __('messages.maintenance_title') }}</h1>
        <p class="mx-auto mt-5 max-w-xl text-lg leading-8 text-zinc-600 dark:text-zinc-300">{{ __('messages.maintenance_body') }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('login') }}" class="rounded-lg bg-zinc-950 px-5 py-3 text-sm font-bold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                {{ __('messages.login') }}
            </a>
            <a href="{{ route('lang.switch', app()->getLocale() === 'th' ? 'en' : 'th') }}" class="rounded-lg border border-zinc-300 px-5 py-3 text-sm font-bold hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900">
                {{ app()->getLocale() === 'th' ? 'EN' : 'TH' }}
            </a>
        </div>
    </section>
</main>
@endsection
