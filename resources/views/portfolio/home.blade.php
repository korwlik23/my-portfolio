@extends('layouts.base')

@php
    $siteName = $settings['site_name'] ?? 'Tewarach Portfolio CMS';
    $publicBrand = trim(str_replace(['Portfolio CMS', 'CMS'], '', $siteName)) ?: 'Tewarach';
    $ownerName = trim($settings['owner_name'] ?? '') ?: $publicBrand;
    $nameWords = preg_split('/\s+/', $ownerName, -1, PREG_SPLIT_NO_EMPTY);
    $brandInitials = count($nameWords) > 1
        ? strtoupper(substr($nameWords[0], 0, 1).substr($nameWords[array_key_last($nameWords)], 0, 1))
        : strtoupper(substr($ownerName, 0, 2));
    $ownerTitle = trim($settings['owner_title_' . $locale] ?? '') ?: trim($settings['owner_title_en'] ?? '');
    $location = trim($settings['location_' . $locale] ?? '') ?: trim($settings['location_en'] ?? '');
    $locationCity = trim(explode(',', $location)[0]);
    $locationCode = trim($settings['location_code'] ?? '');
    $timezone = trim($settings['timezone'] ?? '');
    $utcOffset = $timezone !== '' ? 'UTC' . now($timezone)->format('P') : null;
    $localTimeLabel = collect([$locationCode, $utcOffset])->filter()->implode(' · ');

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

    $availability = $hero?->settings['availability'] ?? __('messages.contact_availability');
    $heroStats = $hero?->settings['stats'] ?? [];
    $aboutHighlights = $about?->settings['highlights_' . $locale] ?? [];
    $resumeThUrl = $resumeUrls['th'] ?? null;
    $resumeEnUrl = $resumeUrls['en'] ?? null;

    $allSkills = $skillsByCategory->flatten(1);
    $coreStackSkills = $allSkills->where('level', 'advanced')->pluck('name')->values()->all()
        ?: $allSkills->pluck('name')->take(7)->values()->all();
    $tickerItems = collect($coreStackSkills)->merge($allSkills->pluck('name'))->unique()->take(24)->values()->all();

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

    $workItems = $experiences->where('kind', 'work')->values();
    $educationItems = $experiences->where('kind', 'education')->values();
    $highlightItems = $experiences->where('kind', 'highlight')->values();
    $careerYears = fmod($career['years'], 1.0) == 0.0 ? (int) $career['years'] : $career['years'];
    $careerLabel = $career['since'] ? __('messages.career_summary', ['years' => $careerYears, 'year' => $career['since']]) : null;

    $deploymentNodes = collect($deployment?->settings['nodes'] ?? [])->filter(fn ($node) => filled($node['name'] ?? null))->values();
    $deploymentSteps = $deployment?->settings['steps'] ?? [];
    $hostNode = $deploymentNodes->firstWhere('layer', 'host')['name'] ?? null;
    $edgeNode = $deploymentNodes->firstWhere('layer', 'edge')['name'] ?? null;

    $normalizeStatus = function ($status): string {
        $value = $status instanceof \UnitEnum ? $status->value : (string) $status;
        return str($value)->lower()->replace(['_', ' '], '-')->toString();
    };
    $statusDotClass = function ($status, bool $isPublic = true) use ($normalizeStatus): string {
        if (! $isPublic) {
            return '';
        }
        return match ($normalizeStatus($status)) {
            'live', 'available', 'published' => 'ok',
            'in-progress', 'progress', 'draft' => 'wait',
            default => '',
        };
    };
    $statusLabelFor = function ($status, bool $isPublic = true) use ($normalizeStatus): string {
        if (! $isPublic) {
            return __('messages.status_private');
        }
        return match ($normalizeStatus($status)) {
            'live', 'available', 'published' => __('messages.status_live'),
            'in-progress', 'progress', 'draft' => __('messages.status_in_progress'),
            'main-starter' => __('messages.status_main_starter'),
            default => __('messages.status_demo'),
        };
    };
    $typeLabelFor = function ($type): ?string {
        if (! $type) return null;
        $value = $type instanceof \UnitEnum ? $type->value : (string) $type;
        return ucfirst(strtolower((string) $value));
    };

    $heroTitleRaw = $hero?->localized('title', $locale) ?? '';
    $titleMarker = trim($settings['hero_highlight_' . $locale] ?? '');
    $heroTitleHtml = e($heroTitleRaw);
    if ($titleMarker !== '' && mb_stripos($heroTitleRaw, $titleMarker) !== false) {
        $heroTitleHtml = preg_replace('/(' . preg_quote(e($titleMarker), '/') . ')/iu', '<em>$1</em>', $heroTitleHtml, 1);
    }

    $siteUrl = rtrim(url('/'), '/');
    $personSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $ownerName,
        'jobTitle' => $settings['owner_title_en'] ?? null,
        'url' => url('/'),
        'email' => $contactEmail,
        'telephone' => $contactPhone,
        'homeLocation' => filled($settings['location_en'] ?? null) ? ['@type' => 'Place', 'name' => $settings['location_en']] : null,
        'knowsAbout' => $coreStackSkills,
        'alumniOf' => $educationItems->pluck('company')->filter()->unique()
            ->map(fn ($name) => ['@type' => 'CollegeOrUniversity', 'name' => $name])->values()->all(),
        'sameAs' => $visibleLinks->pluck('url')
            ->filter(fn ($url) => str_starts_with($url, 'http') && rtrim($url, '/') !== $siteUrl)->values()->all(),
    ], fn ($value) => filled($value));
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@section('canonical_url', $seo?->canonical_url ?: url('/'))
@if($ogImage)
    @section('og_image', $ogImage)
@endif

@section('body_class', 'portfolio-page antialiased min-h-screen')

@push('head')
<link href="https://fonts.bunny.net/css?family=ibm-plex-mono:400,500,600|ibm-plex-sans:400,500,600,700|ibm-plex-sans-condensed:600,700|ibm-plex-sans-thai:400,500,600,700&display=swap" rel="stylesheet" />
@endpush

@push('scripts')
<script type="application/ld+json">
{!! json_encode($personSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('site_nav')
    <a href="#main-content" class="pf-skip">{{ __('messages.skip_to_content') }}</a>
    <header class="pf-nav" data-portfolio-navbar>
        <div class="pf-wrap pf-nav-inner">
            <a href="{{ url('/') }}" class="pf-brand">
                <b>{{ $publicBrand }}</b>
                @if($ownerTitle !== '')
                    <span>/ {{ mb_strtolower($ownerTitle) }}@if($locationCode !== '') · {{ mb_strtolower($locationCode) }}@endif</span>
                @endif
            </a>
            <nav class="pf-nav-links" aria-label="Primary">
                <a href="#about" class="pf-nav-section">{{ __('messages.portfolio_about') }}</a>
                <a href="#projects" class="pf-nav-section">{{ __('messages.portfolio_projects') }}</a>
                <a href="#experience" class="pf-nav-section">{{ __('messages.experience') }}</a>
                <a href="#stack" class="pf-nav-section">{{ __('messages.skills') }}</a>
                <a href="#contact" class="pf-nav-section">{{ __('messages.contact') }}</a>
                <span class="pf-pill" aria-label="{{ $availability }}"><span class="pf-dot ok"></span>OPEN</span>
                <a href="{{ route('lang.switch', 'en') }}" aria-label="{{ __('messages.switch_to_english') }}" @class(['is-current' => $locale === 'en'])>EN</a>
                <span class="pf-sep">/</span>
                <a href="{{ route('lang.switch', 'th') }}" aria-label="{{ __('messages.switch_to_thai') }}" @class(['is-current' => $locale === 'th'])>TH</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="pf-admin">{{ __('messages.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="pf-admin">{{ __('messages.login') }}</a>
                @endauth
            </nav>
        </div>
    </header>
@endsection

@section('body')
<main id="main-content">
    <section class="pf-wrap pf-hero">
        <div class="pf-status-row" role="status">
            <span><b>STATUS</b> <span class="pf-dot ok"></span> ONLINE <span class="pf-eq" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span></span>
            <span><b>{{ date('Y') }}-Q{{ ceil(date('n') / 3) }}</b> {{ strtoupper($availability) }}</span>
            @if($localTimeLabel !== '')
                <span>{{ $localTimeLabel }}</span>
            @endif
        </div>

        <div class="pf-hero-grid">
            <div>
                <h1>{!! $heroTitleHtml !!}</h1>
                <p class="pf-lede">{{ $hero?->localized('subtitle', $locale) }}</p>
                <div class="pf-cta-row">
                    @if($hero?->primary_cta_url)
                        <a href="{{ $hero->primary_cta_url }}" class="pf-cta primary">{{ $hero->localized('primary_cta_text', $locale) ?: __('messages.view_live_projects') }} <span aria-hidden="true">→</span></a>
                    @endif
                    @if($hero?->secondary_cta_url)
                        <a href="{{ $hero->secondary_cta_url }}" class="pf-cta ghost">{{ $hero->localized('secondary_cta_text', $locale) }} <span aria-hidden="true">↗</span></a>
                    @endif
                </div>
            </div>

            <aside class="pf-spec" aria-label="{{ __('messages.core_stack_highlight') }}">
                <div class="pf-spec-head">
                    <span>// current.stack</span>
                    <span><span class="pf-dot ok"></span>RUNNING</span>
                </div>
                @foreach($heroStats as $stat)
                    <div class="pf-spec-row">
                        <span class="pf-k">{{ mb_strtolower($stat['label_' . $locale] ?? $stat['label_en'] ?? '') }}</span>
                        <span class="pf-v">{{ $stat['value'] ?? '' }}</span>
                    </div>
                @endforeach
                @if($careerLabel)
                    <div class="pf-spec-row">
                        <span class="pf-k">{{ mb_strtolower(__('messages.experience')) }}</span>
                        <span class="pf-v">{{ $careerLabel }}</span>
                    </div>
                @endif
                <div class="pf-spec-row">
                    <span class="pf-k">{{ __('messages.portfolio_availability') }}</span>
                    <span class="pf-v pf-avail-value">{{ $availability }}</span>
                </div>
            </aside>
        </div>
    </section>

    @if(count($tickerItems))
        <div class="pf-ticker" aria-hidden="true">
            <div class="pf-ticker-track">
                @foreach([1, 2] as $pass)
                    @foreach($tickerItems as $item)
                        <span>{{ $item }}</span>
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif

    <section id="about" class="pf-wrap pf-about">
        <div class="pf-section-head">
            <span class="pf-num">§ 01</span>
            <h2>{{ __('messages.portfolio_about') }}</h2>
            <span class="pf-right">file: about.md</span>
        </div>

        <div class="pf-about-grid">
            <figure class="pf-portrait" aria-label="{{ __('messages.profile_image_alt', ['name' => $ownerName]) }}">
                @if($about?->image_path)
                    <img src="{{ route('portfolio.media', ['path' => $about->image_path]) }}" alt="{{ __('messages.profile_image_alt', ['name' => $ownerName]) }}" loading="lazy">
                @else
                    <svg viewBox="0 0 160 200" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true" class="pf-portrait-placeholder">
                        <circle cx="80" cy="70" r="34"/>
                        <path d="M24 196c0-32 25-58 56-58s56 26 56 58"/>
                    </svg>
                @endif
                <figcaption class="pf-portrait-caption">
                    <span>fig. {{ $brandInitials }}@if($locationCity !== '') / {{ $locationCity }}@endif</span>
                    <span>{{ date('Y') }}</span>
                </figcaption>
            </figure>

            <div class="pf-about-body">
                <p>{{ $about?->localized('body', $locale) }}</p>

                @if(count($aboutHighlights))
                    <div class="pf-highlights">
                        @foreach($aboutHighlights as $highlight)
                            <div>{{ $highlight }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="pf-facts">
                    <div class="pf-facts-head">
                        <span>// {{ __('messages.quick_facts') }}</span>
                        <span>{{ trans_choice('messages.portfolio_entries_count', $quickFacts->count(), ['count' => $quickFacts->count()]) }}</span>
                    </div>
                    <div class="pf-facts-grid">
                        @foreach($quickFacts as $fact)
                            <div>
                                <span class="pf-k">{{ $fact['label'] }}</span>
                                <span class="pf-v">{{ $fact['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="projects" class="pf-wrap pf-work">
        <div class="pf-section-head">
            <span class="pf-num">§ 02</span>
            <h2>{{ __('messages.featured_projects') }}</h2>
            <span class="pf-right">{{ trans_choice('messages.portfolio_projects_count', $projects->count(), ['count' => $projects->count()]) }}</span>
        </div>

        <div>
            @foreach($projects as $project)
                @php
                    $dotClass = $statusDotClass($project->status ?: 'demo', (bool) $project->is_public);
                    $statusText = $statusLabelFor($project->status ?: 'demo', (bool) $project->is_public);
                    $typeText = $typeLabelFor($project->type);
                @endphp
                <article class="pf-entry">
                    <span class="pf-idx">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <div class="pf-lead-row">
                            @if($dotClass)<span class="pf-dot {{ $dotClass }}"></span>@endif
                            <b>{{ $statusText }}</b>
                            @if($typeText)<span>· {{ $typeText }}</span>@endif
                            @if($project->is_featured)<span>· {{ __('messages.is_featured') }}</span>@endif
                        </div>
                        <h3>{{ $project->name }}</h3>
                        <p class="pf-desc">{{ $project->localized('description', $locale) }}</p>
                        @if($project->techStacks->isNotEmpty())
                            <div class="pf-stack">
                                @foreach($project->techStacks as $tech)
                                    <span>{{ $tech->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="pf-meta">
                        @if($project->image_path)
                            <img class="pf-thumb" src="{{ route('portfolio.media', ['path' => $project->image_path]) }}" alt="{{ __('messages.project_image_alt', ['name' => $project->name]) }}" loading="lazy">
                        @endif
                        @if($project->created_at)
                            <div><b>{{ $project->created_at->year }}</b></div>
                        @endif
                        <div>{{ (bool) $project->is_public ? __('messages.is_public') : __('messages.status_private') }}</div>
                    </div>
                    <div class="pf-actions">
                        @if($project->live_demo_url)
                            <a href="{{ $project->live_demo_url }}" target="_blank" rel="noopener" aria-label="{{ __('messages.live_demo_for', ['name' => $project->name]) }}">{{ __('messages.live_demo') }} →</a>
                        @endif
                        @if($project->localized('case_study', $locale))
                            <details>
                                <summary class="pf-case-summary">{{ __('messages.case_study') }} ↓</summary>
                                <p class="pf-case">{{ $project->localized('case_study', $locale) }}</p>
                            </details>
                        @endif
                        @if($project->github_url)
                            <a href="{{ $project->github_url }}" target="_blank" rel="noopener" aria-label="{{ __('messages.github_for', ['name' => $project->name]) }}">GitHub ↗</a>
                        @else
                            <span aria-label="{{ __('messages.private_repo_for', ['name' => $project->name]) }}">{{ __('messages.private_repo') }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if($starters->isNotEmpty())
            <div class="pf-subhead">
                <h3>{{ __('messages.project_starters') }}</h3>
                <span>{{ trans_choice('messages.portfolio_starters_count', $starters->count(), ['count' => $starters->count()]) }}</span>
            </div>
            @foreach($starters as $starter)
                @php
                    $isMainStarter = (int) $starter->display_order === 1;
                    $dotClass = $statusDotClass($starter->status ?: 'demo', (bool) $starter->is_public);
                    $statusText = $statusLabelFor($starter->status ?: 'demo', (bool) $starter->is_public);
                @endphp
                <article class="pf-starter">
                    <span class="pf-idx">S.{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="pf-nm">{{ $starter->name }}</div>
                    <div class="pf-st">{{ implode(' · ', $starter->stack ?? []) }}</div>
                    <div class="pf-mt">
                        @if($dotClass)<span class="pf-dot {{ $dotClass }}"></span>@endif
                        <b>{{ $statusText }}</b>
                        @if($isMainStarter)<span>· {{ __('messages.status_main_starter') }}</span>@endif
                        @if($starter->demo_url)<a href="{{ $starter->demo_url }}" target="_blank" rel="noopener">{{ __('messages.live_demo') }} →</a>@endif
                        @if($starter->github_url)<a href="{{ $starter->github_url }}" target="_blank" rel="noopener">GitHub ↗</a>@endif
                    </div>
                </article>
            @endforeach
        @endif
    </section>

    @if($experiences->isNotEmpty())
        <section id="experience" class="pf-wrap pf-exp">
            <div class="pf-section-head">
                <span class="pf-num">§ 03</span>
                <h2>{{ __('messages.portfolio_experience_heading') }}</h2>
                <span class="pf-right">{{ $careerLabel }}</span>
            </div>

            @if($workItems->isNotEmpty())
                <div class="pf-subhead">
                    <h3>{{ __('messages.kind_work') }}</h3>
                    <span>{{ trans_choice('messages.portfolio_roles_count', $workItems->count(), ['count' => $workItems->count()]) }}</span>
                </div>
                @include('portfolio.partials.timeline', ['items' => $workItems])
            @endif

            @if($educationItems->isNotEmpty())
                <div class="pf-subhead">
                    <h3>{{ __('messages.kind_education') }}</h3>
                    <span>{{ trans_choice('messages.portfolio_entries_count', $educationItems->count(), ['count' => $educationItems->count()]) }}</span>
                </div>
                @include('portfolio.partials.timeline', ['items' => $educationItems])
            @endif

            @if($highlightItems->isNotEmpty())
                <div class="pf-subhead">
                    <h3>{{ __('messages.experience_highlights') }}</h3>
                    <span>{{ trans_choice('messages.portfolio_entries_count', $highlightItems->count(), ['count' => $highlightItems->count()]) }}</span>
                </div>
                @foreach($highlightItems as $item)
                    <article class="pf-hl">
                        <span class="pf-idx">H.{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h4>{{ $item->localized('title', $locale) }}</h4>
                        <div>
                            <p>{{ implode(' ', $item->bullets($locale)) }}</p>
                            @if(! empty($item->tech_stack))
                                <div class="pf-stack">
                                    @foreach($item->tech_stack as $tech)
                                        <span>{{ $tech }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            @endif
        </section>
    @endif

    <section id="stack" class="pf-wrap pf-skills">
        <div class="pf-section-head">
            <span class="pf-num">§ 04</span>
            <h2>{{ __('messages.portfolio_skills_heading') }}</h2>
            <span class="pf-right">{{ __('messages.portfolio_skills_count', ['skills' => $allSkills->count(), 'groups' => $skillsByCategory->count()]) }}</span>
        </div>

        @if(count($coreStackSkills))
            <div class="pf-core">
                <span class="pf-k">{{ __('messages.core_stack_highlight') }}</span>
                <div class="pf-v">
                    @foreach($coreStackSkills as $skillName)
                        <span>{{ $skillName }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="pf-skill-grid">
            @foreach($skillsByCategory as $category => $skills)
                <div class="pf-skill-cell">
                    <h4>{{ $category }}</h4>
                    <ul>
                        @foreach($skills as $skill)
                            <li @class(['is-core' => $skill->level === 'advanced'])>{{ $skill->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    @if($services->isNotEmpty())
        <section class="pf-services">
            <div class="pf-wrap">
                <div class="pf-section-head pf-section-head--flush">
                    <span class="pf-num">§ 05</span>
                    <h2>{{ __('messages.portfolio_services_heading') }}</h2>
                    <span class="pf-right">{{ trans_choice('messages.portfolio_services_count', $services->count(), ['count' => $services->count()]) }}</span>
                </div>
                <div class="pf-services-list">
                    @foreach($services as $service)
                        <div class="pf-svc">
                            <span class="pf-n">S / {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="pf-t">{{ $service->localized('title', $locale) }}</span>
                            <span class="pf-d">{{ $service->localized('description', $locale) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="deployment" class="pf-deploy">
        <div class="pf-wrap">
            <div class="pf-section-head">
                <span class="pf-num">§ 06</span>
                <h2>{{ $deployment?->localized('title', $locale) ?: __('messages.deployment_case_study') }}</h2>
                <span class="pf-right">{{ __('messages.case_study') }}</span>
            </div>
            @if($deployment?->localized('subtitle', $locale))
                <p class="pf-sub">{{ $deployment->localized('subtitle', $locale) }}</p>
            @endif

            <div class="pf-schematic">
                <div class="pf-schematic-head">
                    <span>// {{ __('messages.deployment_architecture') }}</span>
                    @if($deploymentNodes->isNotEmpty())
                        <span>{{ $deploymentNodes->count() }} nodes</span>
                    @endif
                </div>
                @if($deploymentNodes->isNotEmpty())
                    <ol class="pf-flow">
                        @foreach($deploymentNodes as $node)
                            <li class="pf-node">
                                <span class="pf-nk">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}@if(filled($node['layer'] ?? null)) / {{ $node['layer'] }}@endif</span>
                                <span class="pf-nv">{{ $node['name'] }}</span>
                                @if(filled($node['note'] ?? null))
                                    <span class="pf-ns">{{ $node['note'] }}</span>
                                @endif
                                @unless($loop->last)
                                    <span class="pf-arrow" aria-hidden="true">→</span>
                                @endunless
                            </li>
                        @endforeach
                    </ol>
                @endif
                @if(count($deploymentSteps))
                    <div class="pf-deploy-stack">
                        @foreach($deploymentSteps as $step)
                            <span>{{ $step }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section id="contact" class="pf-wrap pf-contact">
        <div class="pf-section-head">
            <span class="pf-num">§ 07</span>
            <h2>{{ __('messages.contact') }}</h2>
            <span class="pf-right">{{ $localTimeLabel }}</span>
        </div>

        <div class="pf-contact-grid">
            <div>
                <span class="pf-avail">{{ __('messages.contact_availability') }}</span>
                <h3>{{ __('messages.portfolio_contact_heading') }}</h3>
                <p>{{ __('messages.portfolio_contact_body') }}</p>
            </div>

            <aside class="pf-contact-card">
                @if($contactEmail)
                    <div class="pf-row">
                        <span class="pf-k">{{ __('messages.email') }}</span>
                        <span class="pf-v">
                            <a href="mailto:{{ $contactEmail }}" data-no-loader data-email-link data-email="{{ $contactEmail }}">{{ $contactEmail }}</a>
                        </span>
                    </div>
                @endif
                @if($contactPhone)
                    <div class="pf-row">
                        <span class="pf-k">{{ __('messages.urgent_contact') }}</span>
                        <span class="pf-v">
                            <a href="{{ $contactPhoneHref }}" data-no-loader class="pf-phone" aria-label="{{ __('messages.urgent_contact') }} {{ $contactPhone }}">{{ $contactPhone }}</a>
                        </span>
                    </div>
                @endif
                @if($resumeThUrl || $resumeEnUrl)
                    <div class="pf-row">
                        <span class="pf-k">{{ __('messages.portfolio_resume') }}</span>
                        <span class="pf-v pf-dl">
                            @if($resumeThUrl)
                                <a href="{{ $resumeThUrl }}" download>{{ __('messages.download_resume_th') }} ↓</a>
                            @endif
                            @if($resumeEnUrl)
                                <a href="{{ $resumeEnUrl }}" download>{{ __('messages.download_resume_en') }} ↓</a>
                            @endif
                        </span>
                    </div>
                @endif
                @if($visibleLinks->isNotEmpty())
                    <div class="pf-row">
                        <span class="pf-k">{{ __('messages.portfolio_elsewhere') }}</span>
                        <span class="pf-v pf-links">
                            @foreach($visibleLinks as $link)
                                <a href="{{ $link['url'] }}" target="{{ str_starts_with($link['url'], 'mailto:') ? '_self' : '_blank' }}" rel="noopener" aria-label="{{ $link['label'] }}">{{ $link['label'] }} ↗</a>
                            @endforeach
                        </span>
                    </div>
                @endif
                @if($contactEmail)
                    <div class="pf-row">
                        <span class="pf-k">&nbsp;</span>
                        <span id="contact-email-status" class="pf-email-status" role="status" aria-live="polite"></span>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    <footer class="pf-wrap pf-colophon">
        <div>{{ __('messages.portfolio_colophon_built', ['name' => $ownerName, 'year' => date('Y'), 'city' => $location ?: '—']) }}</div>
        <div>{{ __('messages.portfolio_colophon_type') }}</div>
        <div class="pf-colophon-right">
            @if($hostNode && $edgeNode)
                {{ __('messages.portfolio_colophon_served', ['host' => $hostNode, 'edge' => $edgeNode]) }}
            @endif
        </div>
    </footer>
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
