@extends('layouts.base')
@section('title', __('messages.register'))
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
            <h2 class="text-lg font-semibold mb-6">{{ __('messages.register') }}</h2>
            
            @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 text-sm font-medium">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('messages.name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white transition" placeholder="Your Name">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white transition" placeholder="email@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('messages.password') }}</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white transition" placeholder="••••••••">
                    
                    <!-- Password Hint -->
                    <div class="mt-2 space-y-1">
                        <p class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">{{ __('messages.password_requirements') }}</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                            <div class="flex items-center text-[11px] text-zinc-500">
                                <span class="w-1 h-1 rounded-full bg-zinc-300 mr-2"></span>
                                {{ __('messages.password_min_chars', ['count' => 8]) }}
                            </div>
                            <div class="flex items-center text-[11px] text-zinc-500">
                                <span class="w-1 h-1 rounded-full bg-zinc-300 mr-2"></span>
                                {{ __('messages.password_uppercase') }} 
                            </div>
                            <div class="flex items-center text-[11px] text-zinc-500">
                                <span class="w-1 h-1 rounded-full bg-zinc-300 mr-2"></span>
                                {{ __('messages.password_lowercase') }}
                            </div>
                            <div class="flex items-center text-[11px] text-zinc-500">
                                <span class="w-1 h-1 rounded-full bg-zinc-300 mr-2"></span>
                                {{ __('messages.password_numbers') }}
                            </div>
                            <div class="flex items-center text-[11px] text-zinc-500">
                                <span class="w-1 h-1 rounded-full bg-zinc-300 mr-2"></span>
                                {{ __('messages.password_symbols') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('messages.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white transition" placeholder="••••••••">
                </div>
                
                <div class="text-[11px] text-zinc-500 text-center py-2">
                    {!! __('messages.by_registering_agree', ['terms' => route('legal.terms'), 'privacy' => route('legal.privacy')]) !!}
                </div>
                
                <button type="submit" class="w-full py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl text-sm font-semibold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition active:scale-[0.98]">
                    {{ __('messages.register') }}
                </button>

                <div class="text-center mt-4">
                    <p class="text-sm text-zinc-500">
                        {{ __('messages.already_have_account') }} 
                        <a href="{{ route('login') }}" class="text-zinc-900 dark:text-white font-semibold hover:underline">
                            {{ __('messages.login') }}
                        </a>
                    </p>
                </div>
            </form>
        </div>
        <div class="text-center mt-6">
            <button onclick="toggleDarkMode()" class="text-sm text-zinc-400 hover:text-zinc-600 transition">{{ __('messages.toggle_mode') }}</button>
        </div>
    </div>
</div>
@endsection
