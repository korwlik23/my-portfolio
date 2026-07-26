@extends('layouts.base')
@section('title', __('messages.forgot_password') ?? 'Forgot Password')
@section('body')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-zinc-900 dark:bg-white rounded-2xl mb-4">
                @if(!empty($globalSystemSettings['logo_path']))
                    <div class="w-10 h-10 flex items-center justify-center overflow-hidden">
                        <img src="{{ route('portfolio.media', ['path' => $globalSystemSettings['logo_path']]) }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                @else
                    <span class="text-white dark:text-zinc-900 font-bold text-2xl">LS</span>
                @endif
            </div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $globalSystemSettings['brand_name'] ?? config('app.name') }}</h1>
            <p class="text-zinc-500 mt-1 text-sm">{{ __('messages.starter_app_desc') }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-8 shadow-sm">
            <h2 class="text-lg font-semibold mb-2">{{ __('messages.forgot_password') ?? 'Forgot Password' }}</h2>
            <p class="text-sm text-zinc-500 mb-6">{{ __('messages.forgot_password_desc') ?? 'No problem. Just let us know your email address and we will email you a password reset link.' }}</p>
            
            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg text-emerald-600 text-sm font-medium">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 text-sm font-medium">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white transition" placeholder="email@example.com">
                </div>
                <button type="submit" class="w-full py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl text-sm font-semibold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition active:scale-[0.98]">
                    {{ __('messages.send_reset_link') ?? 'Email Password Reset Link' }}
                </button>
            </form>
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition">
                    {{ __('messages.back_to_login') ?? 'Back to Login' }}
                </a>
            </div>
        </div>
        <div class="text-center mt-6">
            <button onclick="toggleDarkMode()" class="text-sm text-zinc-400 hover:text-zinc-600 transition">{{ __('messages.toggle_mode') }}</button>
        </div>
    </div>
</div>
@endsection
