@extends('layouts.base')

@php
    $siteName = $settings['site_name'] ?? 'Tewarach Portfolio CMS';
    $publicBrand = trim(str_replace(['Portfolio CMS', 'CMS'], '', $siteName)) ?: 'Tewarach';
    $brandWords = preg_split('/\s+/', $publicBrand, -1, PREG_SPLIT_NO_EMPTY);
    $brandInitials = count($brandWords) > 1
        ? strtoupper(substr($brandWords[0], 0, 1).substr($brandWords[array_key_last($brandWords)], 0, 1))
        : strtoupper(substr($publicBrand, 0, 2));
    $brandInitials = $brandInitials ?: 'KL';
    $metaTitle = $seo?->metaTitle($locale) ?: $siteName;
    $metaDescription = $seo?->metaDescription($locale) ?: ($hero?->localized('subtitle', $locale) ?? '');
    $ogImage = $seo?->og_image_path ? route('portfolio.media', ['path' => $seo->og_image_path]) : null;
    $rawContactEmail = trim($settings['contact_email'] ?? '');
    $rawPhoneNumber = trim($settings['phone_number'] ?? '');
    $emailLink = $links->first(fn ($link) => $link->type === 'email' && str_starts_with($link->url, 'mailto:'));
    $linkContactEmail = $emailLink ? trim(str_replace('mailto:', '', $emailLink->url)) : '';
    $isPlaceholderEmail = fn (?string $email): bool => blank($email)
        || str_contains(strtolower($email), 'example.com')
        || str_contains(strtolower($email), 'example.test');
    $isPlaceholderPhone = fn (?string $phone): bool => blank($phone)
        || str_contains(strtolower($phone), 'example')
        || strlen(preg_replace('/\D/', '', $phone ?? '')) < 6;
    $normalizeUrl = fn (?string $url): string => rtrim(strtolower(trim($url ?? '')), '/');
    $placeholderUrls = [
        'http://localhost',
        'https://localhost',
        'http://127.0.0.1',
        'https://127.0.0.1',
        'https://github.com',
        'https://www.github.com',
        'https://linkedin.com',
        'https://www.linkedin.com',
        'https://facebook.com',
        'https://www.facebook.com',
    ];
    $isPlaceholderUrl = fn (?string $url): bool => blank($url)
        || in_array($normalizeUrl($url), $placeholderUrls, true)
        || str_contains($normalizeUrl($url), 'example.com')
        || str_contains($normalizeUrl($url), 'example.test');
    $contactEmail = ! $isPlaceholderEmail($rawContactEmail)
        ? $rawContactEmail
        : (! $isPlaceholderEmail($linkContactEmail) ? $linkContactEmail : null);
    $contactPhone = ! $isPlaceholderPhone($rawPhoneNumber) ? $rawPhoneNumber : null;
    $contactPhoneHref = $contactPhone ? 'tel:' . preg_replace('/[^\d+]/', '', $contactPhone) : null;
    $settingsLinks = collect([
        ['type' => 'github', 'label' => 'GitHub', 'url' => $settings['github_url'] ?? null],
        ['type' => 'linkedin', 'label' => 'LinkedIn', 'url' => $settings['linkedin_url'] ?? null],
        ['type' => 'facebook', 'label' => 'Facebook', 'url' => $settings['facebook_url'] ?? null],
        ['type' => 'discord', 'label' => 'Discord', 'url' => $settings['discord_url'] ?? null],
        ['type' => 'line', 'label' => 'Line', 'url' => $settings['line_url'] ?? null],
    ])->filter(fn ($link) => ! $isPlaceholderUrl($link['url']));
    $customLinks = $links->filter(function ($link) use ($isPlaceholderEmail, $isPlaceholderUrl) {
        if ($isPlaceholderUrl($link->url)) {
            return false;
        }

        if ($link->type !== 'email') {
            return true;
        }

        return ! $isPlaceholderEmail(str_replace('mailto:', '', $link->url));
    })->map(fn ($link) => ['type' => $link->type, 'label' => $link->label ?: ucfirst($link->type), 'url' => $link->url]);
    $visibleLinks = $settingsLinks->merge($customLinks)->unique('type')->values();
    $availability = $hero?->settings['availability'] ?? 'Available for selected work';
    $heroStats = $hero?->settings['stats'] ?? [];
    $aboutHighlights = $about?->settings['highlights_' . $locale] ?? [];
    $deploymentSteps = $deployment?->settings['steps'] ?? ['Cloudflare DNS', 'Azure VM', 'Nginx', 'Laravel / Next.js', 'MySQL'];
    $resumeThUrl = $resumeUrls['th'] ?? null;
    $resumeEnUrl = $resumeUrls['en'] ?? null;
    $allSkills = $skillsByCategory->flatten(1);
    $coreStackPriority = ['Laravel', 'PHP', 'MySQL', 'MariaDB', 'Next.js', 'Tailwind CSS', 'Azure VM', 'Cloudflare'];
    $coreStackSkills = collect($coreStackPriority)
        ->map(fn ($name) => $allSkills->first(fn ($skill) => str_contains(strtolower($skill->name), strtolower($name))))
        ->filter()
        ->unique('name')
        ->take(7)
        ->pluck('name')
        ->values()
        ->all();
    $coreStackSkills = $coreStackSkills ?: ['Laravel', 'Next.js', 'MySQL', 'Tailwind CSS', 'Azure VM', 'Cloudflare'];
    $quickFactsFromAbout = collect($about?->settings['quick_facts_' . $locale] ?? [])
        ->map(function ($fact) {
            $label = trim((string) ($fact['label'] ?? ''));
            $value = trim((string) ($fact['value'] ?? ''));

            return ($label !== '' && $value !== '') ? ['label' => $label, 'value' => $value] : null;
        })
        ->filter()
        ->values();
    $quickFacts = $quickFactsFromAbout->isNotEmpty()
        ? $quickFactsFromAbout
        : collect([
            ['label' => __('messages.quick_fact_location'), 'value' => __('messages.quick_fact_location_value')],
            ['label' => __('messages.quick_fact_role'), 'value' => __('messages.quick_fact_role_value')],
            ['label' => __('messages.quick_fact_core_stack'), 'value' => __('messages.quick_fact_core_stack_value')],
            ['label' => __('messages.quick_fact_focus'), 'value' => __('messages.quick_fact_focus_value')],
            ['label' => __('messages.quick_fact_available'), 'value' => __('messages.quick_fact_available_value')],
        ]);
    $deploymentDiagram = ['Cloudflare DNS', 'Azure VM', 'Nginx Reverse Proxy', 'Laravel / Next.js Apps', 'MySQL / MariaDB Databases'];
    $statusBadge = function (string|\UnitEnum $status, bool $isPublic = true): array {
        if (! $isPublic) {
            return ['label' => __('messages.status_private'), 'class' => 'border-zinc-300 bg-zinc-100 text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300'];
        }

        $statusValue = $status instanceof \UnitEnum ? $status->value : $status;
        $normalized = str($statusValue)->lower()->replace(['_', ' '], '-')->toString();

        return match ($normalized) {
            'live', 'available', 'published' => ['label' => __('messages.status_live'), 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300'],
            'main-starter' => ['label' => __('messages.status_main_starter'), 'class' => 'border-blue-300 bg-blue-100 text-blue-800 dark:border-blue-800 dark:bg-blue-950/80 dark:text-blue-200'],
            'in-progress', 'progress', 'draft' => ['label' => __('messages.status_in_progress'), 'class' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950/60 dark:text-amber-300'],
            'architecture' => ['label' => __('messages.status_architecture'), 'class' => 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900 dark:bg-violet-950/60 dark:text-violet-300'],
            'case-study' => ['label' => __('messages.case_study'), 'class' => 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900 dark:bg-violet-950/60 dark:text-violet-300'],
            'dashboard' => ['label' => 'Dashboard', 'class' => 'border-cyan-200 bg-cyan-50 text-cyan-700 dark:border-cyan-900 dark:bg-cyan-950/60 dark:text-cyan-300'],
            'website' => ['label' => 'Website', 'class' => 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900 dark:bg-sky-950/60 dark:text-sky-300'],
            'service' => ['label' => 'Service', 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300'],
            'tool' => ['label' => 'Tool', 'class' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950/60 dark:text-amber-300'],
            'saas' => ['label' => 'SaaS', 'class' => 'border-fuchsia-200 bg-fuchsia-50 text-fuchsia-700 dark:border-fuchsia-900 dark:bg-fuchsia-950/60 dark:text-fuchsia-300'],
            default => ['label' => __('messages.status_demo'), 'class' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950/60 dark:text-blue-300'],
        };
    };
    $projectBadges = fn ($project) => collect([
        $statusBadge($project->status ?: 'demo', (bool) $project->is_public),
        $project->type ? $statusBadge($project->type, (bool) $project->is_public) : null,
        (bool) $project->is_public ? ['label' => __('messages.is_public'), 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300'] : null,
        (bool) $project->is_featured ? ['label' => __('messages.is_featured'), 'class' => 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900 dark:bg-violet-950/60 dark:text-violet-300'] : null,
    ])->filter()->unique('label')->values();
    $starterBadges = fn ($starter) => collect([
        ((int) $starter->display_order === 1) ? $statusBadge('main-starter', (bool) $starter->is_public) : null,
        $statusBadge($starter->status ?: 'demo', (bool) $starter->is_public),
        str($starter->name.' '.implode(' ', $starter->stack ?? []))->lower()->contains(['cloud', 'deploy', 'azure', 'nginx'])
            ? $statusBadge('architecture', (bool) $starter->is_public)
            : null,
    ])->filter()->unique('label')->values();
    $personSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $publicBrand,
        'jobTitle' => 'Fullstack Developer',
        'url' => url('/'),
        'knowsAbout' => ['Laravel', 'PHP', 'MySQL', 'Next.js', 'Tailwind CSS', 'Azure VM', 'Cloudflare', 'Nginx'],
    ];
    if ($contactEmail) {
        $personSchema['email'] = $contactEmail;
    }
    if ($contactPhone) {
        $personSchema['telephone'] = $contactPhone;
    }
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@section('canonical_url', $seo?->canonical_url ?: url('/'))
@if($ogImage)
    @section('og_image', $ogImage)
@endif

@push('scripts')
<script type="application/ld+json">
{!! json_encode($personSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('site_nav')
    <header id="site-navbar" data-portfolio-navbar class="sticky top-0 z-50 border-b border-zinc-200/80 bg-white/90 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-blue-600 focus:px-4 focus:py-2 focus:text-sm focus:font-bold focus:text-white">{{ __('messages.skip_to_content') }}</a>
        <nav class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-zinc-950 text-sm font-bold text-white shadow-sm dark:bg-white dark:text-zinc-950">{{ $brandInitials }}</span>
                <span class="truncate font-semibold tracking-tight">{{ $publicBrand }}</span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('lang.switch', 'en') }}" aria-label="{{ __('messages.switch_to_english') }}" class="rounded-lg px-3 py-2 text-xs font-bold {{ $locale === 'en' ? 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900' }}">EN</a>
                <a href="{{ route('lang.switch', 'th') }}" aria-label="{{ __('messages.switch_to_thai') }}" class="rounded-lg px-3 py-2 text-xs font-bold {{ $locale === 'th' ? 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900' }}">TH</a>
                <button type="button" onclick="toggleDarkMode()" class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950 dark:hover:bg-zinc-900 dark:hover:text-white" aria-label="{{ __('messages.toggle_mode') }}">
                    <x-icon name="sun" class="h-5 w-5" />
                </button>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="hidden rounded-lg border border-zinc-200 px-3 py-2 text-xs font-bold hover:bg-zinc-100 dark:border-zinc-800 dark:hover:bg-zinc-900 sm:inline-flex">{{ __('messages.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg border border-zinc-200 px-3 py-2 text-xs font-bold hover:bg-zinc-100 dark:border-zinc-800 dark:hover:bg-zinc-900 sm:inline-flex">{{ __('messages.login') }}</a>
                @endauth
            </div>
        </nav>
    </header>
@endsection

@section('body')
<main id="main-content" class="min-h-screen overflow-x-hidden bg-white text-zinc-950 dark:bg-zinc-950 dark:text-zinc-50">
    <section class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-12 sm:px-6 sm:py-14 lg:min-h-[calc(82svh-4rem)] lg:grid-cols-[1.1fr_.9fr] lg:gap-12 lg:py-16">
        <div class="min-w-0">
            <p class="mb-5 inline-block max-w-full whitespace-normal break-words rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold leading-5 text-blue-700 shadow-sm dark:border-blue-900 dark:bg-blue-950/50 dark:text-blue-300">{{ $availability }}</p>
            <h1 class="max-w-full break-words text-4xl font-bold leading-tight tracking-tight sm:max-w-4xl sm:text-6xl">{{ $hero?->localized('title', $locale) }}</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-zinc-600 dark:text-zinc-300">{{ $hero?->localized('subtitle', $locale) }}</p>
            <div class="mt-8 flex min-w-0 flex-wrap gap-3">
                @if($hero?->primary_cta_url)
                    <a href="{{ $hero->primary_cta_url }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-950 sm:w-auto">{{ $hero->localized('primary_cta_text', $locale) ?: __('messages.view_live_projects') }}</a>
                @endif
                @if($hero?->secondary_cta_url)
                    <a href="{{ $hero->secondary_cta_url }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-zinc-300 bg-white px-5 py-3 text-sm font-bold transition hover:border-zinc-500 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-950 dark:hover:border-zinc-500 dark:hover:bg-zinc-900 sm:w-auto">{{ $hero->localized('secondary_cta_text', $locale) }}</a>
                @endif
            </div>
        </div>
        <div class="grid min-w-0 gap-4 sm:grid-cols-3 lg:grid-cols-1">
            @foreach($heroStats as $stat)
                <article class="rounded-lg border border-zinc-200 bg-zinc-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-white dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-900 dark:hover:bg-zinc-900/80">
                    <p class="text-xs font-bold uppercase text-blue-600 dark:text-blue-300">{{ $stat['label_' . $locale] ?? $stat['label_en'] ?? '' }}</p>
                    <p class="mt-2 text-2xl font-bold">{{ $stat['value'] ?? '' }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section id="about" class="scroll-mt-24 border-y border-zinc-200 bg-zinc-50 py-14 dark:border-zinc-800 dark:bg-zinc-900/50 sm:py-16">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[.8fr_1.2fr]">
            <div>
                <p class="text-sm font-bold uppercase text-blue-600">{{ __('messages.portfolio_about') }}</p>
                <h2 class="mt-3 text-3xl font-bold">{{ $about?->localized('title', $locale) }}</h2>
                @if($about?->image_path)
                    <div class="mt-6 overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <img src="{{ route('portfolio.media', ['path' => $about->image_path]) }}" alt="{{ __('messages.profile_image_alt', ['name' => $publicBrand]) }}" loading="lazy" class="aspect-[4/5] w-full object-cover">
                    </div>
                @endif
            </div>
            <div>
                <p class="text-lg leading-8 text-zinc-700 dark:text-zinc-300">{{ $about?->localized('body', $locale) }}</p>
                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    @foreach($aboutHighlights as $highlight)
                        <div class="rounded-lg border border-zinc-200 bg-white p-4 text-sm font-medium leading-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">{{ $highlight }}</div>
                    @endforeach
                </div>
                <div class="mt-8 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                    <h3 class="text-sm font-bold uppercase text-zinc-500 dark:text-zinc-400">{{ __('messages.quick_facts') }}</h3>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach($quickFacts as $fact)
                            <div class="rounded-lg border border-zinc-100 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/70">
                                <p class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400">{{ $fact['label'] }}</p>
                                <p class="mt-1 text-sm font-semibold leading-6">{{ $fact['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="projects" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-14 sm:px-6 sm:py-16">
        <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase text-blue-600">{{ __('messages.portfolio_projects') }}</p>
                <h2 class="mt-2 text-3xl font-bold">{{ __('messages.featured_projects') }}</h2>
            </div>
        </div>
        <div class="grid gap-5 lg:grid-cols-3">
            @foreach($projects as $project)
                @php($badges = $projectBadges($project))
                <article class="group flex min-h-full flex-col rounded-lg border border-zinc-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl hover:shadow-zinc-950/5 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-900 dark:hover:shadow-blue-950/10">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        @foreach($badges as $badge)
                            <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        @endforeach
                    </div>
                    @if($project->image_path)
                        <div class="mb-5 overflow-hidden rounded-lg border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-950">
                            <img src="{{ route('portfolio.media', ['path' => $project->image_path]) }}" alt="{{ __('messages.project_image_alt', ['name' => $project->name]) }}" loading="lazy" class="aspect-video w-full object-cover">
                        </div>
                    @endif
                    <h3 class="text-xl font-bold tracking-tight">{{ $project->name }}</h3>
                    <p class="mt-3 flex-1 text-sm leading-7 text-zinc-600 dark:text-zinc-400">{{ $project->localized('description', $locale) }}</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($project->techStacks as $tech)
                            <span class="rounded-full border border-zinc-200 px-2.5 py-1 text-xs dark:border-zinc-700">{{ $tech->name }}</span>
                        @endforeach
                    </div>
                    <div class="mt-6 flex flex-wrap gap-2 text-sm font-bold">
                        @if($project->live_demo_url)<a class="inline-flex min-h-10 items-center rounded-lg bg-blue-600 px-3.5 py-2 text-white transition hover:bg-blue-500" href="{{ $project->live_demo_url }}" target="_blank" rel="noopener" aria-label="{{ __('messages.live_demo_for', ['name' => $project->name]) }}">{{ __('messages.live_demo') }}</a>@endif
                        @if($project->localized('case_study', $locale))
                            <details class="w-full min-w-0 max-w-full sm:w-auto">
                                <summary class="inline-flex min-h-10 cursor-pointer list-none items-center rounded-lg border border-zinc-300 bg-white px-3.5 py-2 text-zinc-800 transition hover:border-blue-300 hover:text-blue-600 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-blue-800 dark:hover:text-blue-300">{{ __('messages.case_study') }}</summary>
                                <p class="mt-3 break-words rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-sm font-medium leading-7 text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300">{{ $project->localized('case_study', $locale) }}</p>
                            </details>
                        @endif
                        @if($project->github_url)
                            <a class="inline-flex min-h-10 items-center rounded-lg border border-zinc-300 bg-white px-3.5 py-2 text-zinc-800 transition hover:border-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500" href="{{ $project->github_url }}" target="_blank" rel="noopener" aria-label="{{ __('messages.github_for', ['name' => $project->name]) }}">GitHub</a>
                        @else
                            <span class="inline-flex min-h-10 items-center rounded-lg border border-zinc-200 bg-zinc-50 px-3.5 py-2 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400" aria-label="{{ __('messages.private_repo_for', ['name' => $project->name]) }}">{{ __('messages.private_repo') }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="bg-zinc-50 py-14 dark:bg-zinc-900/50 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <p class="text-sm font-bold uppercase text-blue-600">{{ __('messages.portfolio_starters') }}</p>
            <h2 class="mt-2 text-3xl font-bold">{{ __('messages.project_starters') }}</h2>
            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($starters as $starter)
                    @php($badges = $starterBadges($starter))
                    @php($isMainStarter = (int) $starter->display_order === 1)
                    <article class="rounded-lg border p-5 shadow-sm transition hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl hover:shadow-zinc-950/5 dark:hover:border-blue-900 {{ $isMainStarter ? 'border-blue-300 bg-blue-50/80 ring-1 ring-blue-100 dark:border-blue-800 dark:bg-blue-950/20 dark:ring-blue-950' : 'border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950' }}">
                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            @foreach($badges as $badge)
                                <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            @endforeach
                        </div>
                        <h3 class="font-bold">{{ $starter->name }}</h3>
                        <p class="mt-3 text-sm leading-7 text-zinc-600 dark:text-zinc-400">{{ $starter->localized('description', $locale) }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach(($starter->stack ?? []) as $item)
                                <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs dark:bg-zinc-800">{{ $item }}</span>
                            @endforeach
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2 text-sm font-bold">
                            @if($starter->demo_url)<a class="inline-flex min-h-10 items-center rounded-lg bg-blue-600 px-3.5 py-2 text-white transition hover:bg-blue-500" href="{{ $starter->demo_url }}" target="_blank" rel="noopener">{{ __('messages.live_demo') }}</a>@endif
                            @if($starter->github_url)<a class="inline-flex min-h-10 items-center rounded-lg border border-zinc-300 bg-white px-3.5 py-2 transition hover:border-zinc-500 dark:border-zinc-700 dark:bg-zinc-950" href="{{ $starter->github_url }}" target="_blank" rel="noopener">GitHub</a>@endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 sm:py-16 lg:grid-cols-[.8fr_1.2fr] lg:gap-12">
        <div>
            <p class="text-sm font-bold uppercase text-blue-600">{{ __('messages.skills') }}</p>
            <h2 class="mt-2 text-3xl font-bold">{{ __('messages.portfolio_skills_heading') }}</h2>
        </div>
        <div>
            <article class="mb-5 rounded-lg border border-blue-200 bg-blue-50 p-5 shadow-sm dark:border-blue-900 dark:bg-blue-950/30">
                <h3 class="text-sm font-bold uppercase text-blue-700 dark:text-blue-300">{{ __('messages.core_stack_highlight') }}</h3>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($coreStackSkills as $skillName)
                        <span class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-zinc-800 shadow-sm dark:bg-zinc-950 dark:text-zinc-100">{{ $skillName }}</span>
                    @endforeach
                </div>
            </article>
            <div class="grid gap-5 md:grid-cols-2">
                @foreach($skillsByCategory as $category => $skills)
                    <article class="rounded-lg border border-zinc-200 p-5 shadow-sm transition hover:border-blue-200 dark:border-zinc-800 dark:hover:border-blue-900">
                        <h3 class="font-bold">{{ $category }}</h3>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($skills as $skill)
                                <span class="rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-semibold dark:bg-zinc-900">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-y border-zinc-200 bg-zinc-50 py-14 dark:border-zinc-800 dark:bg-zinc-900/50 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <p class="text-sm font-bold uppercase text-blue-600">{{ __('messages.services') }}</p>
            <h2 class="mt-2 text-3xl font-bold">{{ __('messages.portfolio_services_heading') }}</h2>
            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($services as $service)
                    <article class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl hover:shadow-zinc-950/5 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-blue-900">
                        <h3 class="font-bold">{{ $service->localized('title', $locale) }}</h3>
                        <p class="mt-3 text-sm leading-7 text-zinc-600 dark:text-zinc-400">{{ $service->localized('description', $locale) }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="experience" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-14 sm:px-6 sm:py-16">
        <p class="text-sm font-bold uppercase text-blue-600">{{ __('messages.experience') }}</p>
        <h2 class="mt-2 text-3xl font-bold">{{ __('messages.experience_highlights') }}</h2>
        <div class="mt-8 grid gap-5 lg:grid-cols-3">
            @foreach($experiences as $experience)
                <article class="rounded-lg border border-zinc-200 p-5 shadow-sm transition hover:border-blue-200 dark:border-zinc-800 dark:hover:border-blue-900">
                    <p class="text-xs font-bold uppercase text-zinc-500">{{ $experience->period }}</p>
                    <h3 class="mt-2 text-lg font-bold leading-7">{{ $experience->localized('title', $locale) }}</h3>
                    <p class="mt-3 text-sm leading-7 text-zinc-600 dark:text-zinc-400">{{ $experience->localized('description', $locale) }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section id="deployment" class="scroll-mt-28 bg-zinc-950 pb-14 pt-20 text-white sm:pb-16 sm:pt-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <p class="text-sm font-bold uppercase text-blue-300">{{ __('messages.deployment_case_study') }}</p>
            <h2 class="mt-2 text-3xl font-bold">{{ $deployment?->localized('title', $locale) }}</h2>
            <p class="mt-4 max-w-3xl leading-7 text-zinc-300">{{ $deployment?->localized('subtitle', $locale) }}</p>
            <div class="mt-10 rounded-lg border border-white/10 bg-white/[.03] p-5">
                <h3 class="text-sm font-bold uppercase text-blue-200">{{ __('messages.deployment_architecture') }}</h3>
                <div class="mt-5 flex flex-col gap-3 lg:flex-row lg:items-stretch">
                    @foreach($deploymentDiagram as $step)
                        <div class="rounded-lg border border-white/10 bg-zinc-900 p-4 text-sm font-bold text-white shadow-lg shadow-black/10 lg:flex-1">
                            <span class="block text-blue-200">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="mt-2 block leading-6">{{ $step }}</span>
                        </div>
                        @unless($loop->last)
                            <div class="flex items-center justify-center text-blue-200" aria-hidden="true">
                                <span class="grid h-8 w-8 place-items-center rounded-full border border-white/10 bg-zinc-950 text-sm lg:hidden">&darr;</span>
                                <span class="hidden h-10 w-10 place-items-center rounded-full border border-white/10 bg-zinc-950 text-sm lg:grid">&rarr;</span>
                            </div>
                        @endunless
                    @endforeach
                </div>
            </div>
            <div class="mt-8 grid gap-3 md:grid-cols-4 lg:grid-cols-7">
                @foreach($deploymentSteps as $step)
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4 text-sm font-bold text-zinc-100">{{ $step }}</div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="contact" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-14 sm:px-6 sm:py-16">
        <div class="grid gap-6 rounded-lg border border-zinc-200 bg-zinc-50 p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8 lg:grid-cols-[1.2fr_.8fr] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase text-blue-600">{{ __('messages.contact') }}</p>
                <h2 class="mt-2 text-3xl font-bold">{{ __('messages.portfolio_contact_heading') }}</h2>
                <p class="mt-4 max-w-2xl leading-7 text-zinc-600 dark:text-zinc-400">{{ __('messages.portfolio_contact_body') }}</p>
                <p class="mt-4 inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-300">{{ __('messages.contact_availability') }}</p>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-950">
                @if($contactEmail)
                    <p class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400">{{ __('messages.email') }}</p>
                    <a href="mailto:{{ $contactEmail }}" data-no-loader data-email-link data-email="{{ $contactEmail }}" class="mt-2 block break-all text-lg font-bold text-blue-600 hover:underline dark:text-blue-300">{{ $contactEmail }}</a>
                @else
                    <p class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400">{{ __('messages.contact_links') }}</p>
                    <p class="mt-2 text-sm font-semibold leading-6 text-zinc-700 dark:text-zinc-300">{{ __('messages.contact_email_not_configured') }}</p>
                @endif
                @if($contactPhone)
                    <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/30">
                        <p class="text-xs font-bold uppercase text-blue-700 dark:text-blue-300">{{ __('messages.urgent_contact') }}</p>
                        <a href="{{ $contactPhoneHref }}" data-no-loader class="mt-2 block break-all text-lg font-bold text-zinc-950 hover:text-blue-700 hover:underline dark:text-white dark:hover:text-blue-300" aria-label="{{ __('messages.urgent_contact') }} {{ $contactPhone }}">
                            {{ $contactPhone }}
                        </a>
                    </div>
                @endif
                <div class="mt-5 flex flex-wrap gap-3">
                    @if($resumeThUrl)
                        <a href="{{ $resumeThUrl }}" download class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-bold transition hover:border-blue-300 hover:text-blue-600 dark:border-zinc-700 dark:hover:border-blue-800 dark:hover:text-blue-300 sm:w-auto">{{ __('messages.download_resume_th') }}</a>
                    @endif
                    @if($resumeEnUrl)
                        <a href="{{ $resumeEnUrl }}" download class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-bold transition hover:border-blue-300 hover:text-blue-600 dark:border-zinc-700 dark:hover:border-blue-800 dark:hover:text-blue-300 sm:w-auto">{{ __('messages.download_resume_en') }}</a>
                    @endif
                </div>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach($visibleLinks as $link)
                        <a href="{{ $link['url'] }}" target="{{ str_starts_with($link['url'], 'mailto:') ? '_self' : '_blank' }}" rel="noopener" class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm font-bold hover:border-blue-300 hover:text-blue-600 dark:border-zinc-700 dark:bg-zinc-950" aria-label="{{ $link['label'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </div>
                @if($contactEmail)
                    <p id="contact-email-status" class="mt-4 text-sm font-semibold text-emerald-700 dark:text-emerald-300" role="status" aria-live="polite"></p>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
@if($contactEmail)
<script>
    (function () {
        var links = document.querySelectorAll('[data-email-link]');
        var status = document.getElementById('contact-email-status');

        if (!links.length || !status) {
            return;
        }

        function setStatus(message) {
            status.textContent = message;
            window.clearTimeout(status.dataset.timer);
            status.dataset.timer = window.setTimeout(function () {
                status.textContent = '';
            }, 4500);
        }

        links.forEach(function (link) {
            link.addEventListener('click', function () {
                var email = link.dataset.email;

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(email)
                        .then(function () {
                            setStatus(@json(__('messages.email_copied')));
                        })
                        .catch(function () {
                            setStatus(@json(__('messages.email_opening')));
                        });

                    return;
                }

                setStatus(@json(__('messages.email_opening')));
            });
        });
    })();
</script>
@endif
@endpush
