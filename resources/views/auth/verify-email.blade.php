@extends('layouts.base')
@section('title', __('messages.verify_email'))
@section('body')
<div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-zinc-50 via-white to-zinc-100 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950">
    <div class="w-full max-w-md text-center">
        <!-- Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-emerald-100 dark:bg-emerald-900/30 mb-8 animate-bounce">
            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <!-- Content -->
        <h1 class="text-3xl font-black mb-4">{{ __('messages.verify_email_title') }}</h1>
        <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed mb-10">
            {{ __('messages.verify_email_desc') }}
        </p>

        @if (session('success'))
            <div class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Actions -->
        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full py-4 bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-900 rounded-xl text-sm font-bold transition-all duration-300 hover:shadow-xl hover:-translate-y-1 active:translate-y-0">
                    {{ __('messages.resend_verification_email') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-4 text-zinc-500 hover:text-red-500 dark:hover:text-red-400 text-sm font-medium transition-colors">
                    {{ __('messages.logout') }}
                </button>
            </form>
        </div>

        <!-- Help -->
        <p class="mt-12 text-xs text-zinc-400">
            {{ __('messages.verify_help') }}
        </p>
    </div>
</div>
@endsection
