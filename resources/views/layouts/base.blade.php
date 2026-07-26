<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel Starter'))</title>
    <meta name="description" content="@yield('meta_description', config('app.name', 'Laravel Starter'))">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title', config('app.name', 'Laravel Starter')))">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description', config('app.name', 'Laravel Starter')))">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    @hasSection('og_image')
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|sarabun:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
        if (localStorage.getItem('largeTextMode') === 'true') {
            document.documentElement.classList.add('large-text');
        }
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen">
    @yield('site_nav')
    @yield('body')
    @include('components.page-loader')
    @stack('scripts')
</body>
</html>
