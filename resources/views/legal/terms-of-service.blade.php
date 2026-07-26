@extends('layouts.public')

@section('title', __('messages.terms_of_service') . ' — ' . config('app.name'))
@section('meta_description', __('messages.terms_of_service'))

@section('nav-actions')
    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">{{ __('messages.login') }}</a>
@endsection

@section('body')
<article class="max-w-3xl mx-auto px-6 py-16">
    <h1 class="text-3xl font-bold mb-2">{{ __('messages.terms_of_service') }}</h1>
    <p class="text-sm text-zinc-500 mb-10">{{ __('messages.legal_last_updated') }}: {{ date('Y-m-d') }}</p>

    <div class="prose prose-zinc dark:prose-invert max-w-none space-y-8">
        <section>
            <h2 class="mb-3 text-xl font-semibold">1. {{ __('messages.tos_section_acceptance') }}</h2>
            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('messages.legal_terms_intro_starter') }}</p>
        </section>

        <section>
            <h2 class="mb-3 text-xl font-semibold">2. {{ __('messages.tos_section_account') }}</h2>
            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('messages.legal_terms_accounts_starter') }}</p>
        </section>

        <section>
            <h2 class="mb-3 text-xl font-semibold">3. {{ __('messages.tos_section_prohibited') }}</h2>
            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('messages.legal_terms_use_starter') }}</p>
        </section>

        <section>
            <h2 class="mb-3 text-xl font-semibold">4. {{ __('messages.tos_section_payment') }}</h2>
            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('messages.legal_terms_commerce_starter') }}</p>
        </section>

        <section>
            <h2 class="mb-3 text-xl font-semibold">5. {{ __('messages.tos_section_contact') }}</h2>
            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('messages.legal_contact_starter') }}</p>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('messages.landing_contact_email') }}: <a href="mailto:support@example.com" class="underline">support@example.com</a></p>
        </section>
    </div>
</article>
@endsection
