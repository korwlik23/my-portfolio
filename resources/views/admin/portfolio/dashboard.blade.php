@extends('layouts.app')

@section('page-title', __('messages.portfolio_cms'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($stats as $label => $value)
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500">{{ __('messages.' . $label) }}</p>
                <p class="mt-2 text-3xl font-bold">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[.9fr_1.1fr]">
        <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="font-semibold">{{ __('messages.quick_actions') }}</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach([
                    route('admin.portfolio.settings') => __('messages.site_settings'),
                    route('admin.portfolio.content.edit', 'hero') => __('messages.hero_content'),
                    route('admin.portfolio.content.edit', 'about') => __('messages.about_content'),
                    route('admin.portfolio.content.edit', 'deployment') => __('messages.deployment_content'),
                    route('admin.portfolio.resources.index', 'projects') => __('messages.projects'),
                    route('admin.portfolio.resources.index', 'starters') => __('messages.starters'),
                    route('admin.portfolio.resources.index', 'skills') => __('messages.skills'),
                    route('admin.portfolio.resources.index', 'services') => __('messages.services'),
                    route('admin.portfolio.resources.index', 'experiences') => __('messages.experience'),
                    route('admin.portfolio.resources.index', 'links') => __('messages.contact_links'),
                    route('admin.portfolio.seo.index') => __('messages.seo_settings'),
                ] as $url => $label)
                    <a href="{{ $url }}" class="rounded-lg border border-zinc-200 p-4 text-sm font-semibold hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800">{{ $label }}</a>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                <h2 class="font-semibold">{{ __('messages.featured_projects') }}</h2>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($recentProjects as $project)
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div>
                            <p class="font-medium">{{ $project->name }}</p>
                            <p class="text-sm text-zinc-500">{{ $project->status instanceof \UnitEnum ? $project->status->value : $project->status }} · {{ $project->type }}</p>
                        </div>
                        <a href="{{ route('admin.portfolio.resources.edit', ['projects', $project->id]) }}" class="text-sm font-semibold text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-zinc-500">{{ __('messages.no_data') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
