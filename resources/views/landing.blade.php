@extends('layouts.base')

@section('title', config('app.name'))

@section('body')
<main class="min-h-screen bg-white text-zinc-900 dark:bg-zinc-950 dark:text-white">
    <section class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-16">
        <div class="max-w-3xl">
            <p class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500">{{ config('app.name') }}</p>
            <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">{{ __('messages.starter_hero_title') }}</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-zinc-600 dark:text-zinc-300">
                {{ __('messages.starter_hero_body') }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-zinc-900 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">{{ __('messages.dashboard') }}</a>
                @else
                    <a href="{{ route('register') }}" class="rounded-lg bg-zinc-900 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">{{ __('messages.register') }}</a>
                    <a href="{{ route('login') }}" class="rounded-lg border border-zinc-300 px-5 py-3 text-sm font-semibold hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900">{{ __('messages.login') }}</a>
                @endauth
            </div>
        </div>

        <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                __('messages.login') => __('messages.starter_feature_auth'),
                __('messages.roles') => __('messages.starter_feature_roles'),
                __('messages.settings') => __('messages.starter_feature_settings'),
                __('messages.health') => __('messages.starter_feature_health'),
            ] as $title => $body)
                <article class="rounded-lg border border-zinc-200 p-5 dark:border-zinc-800">
                    <h2 class="font-semibold">{{ $title }}</h2>
                    <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $body }}</p>
                </article>
            @endforeach
        </div>
    </section>
</main>
@endsection
