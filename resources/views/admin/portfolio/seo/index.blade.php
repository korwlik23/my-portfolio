@extends('layouts.app')

@section('page-title', __('messages.seo_settings'))

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.portfolio.seo.create') }}" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.create') }}</a>
    </div>
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-800">
                    <th class="px-5 py-3">{{ __('messages.page_key') }}</th>
                    <th class="px-5 py-3">{{ __('messages.meta_title_en') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('messages.manage') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($seoSettings as $seo)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $seo->page_key }}</td>
                        <td class="px-5 py-3 text-zinc-500">{{ $seo->meta_title_en }}</td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.portfolio.seo.edit', $seo) }}" class="rounded-md px-2 py-1 text-xs font-medium text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">{{ __('messages.edit') }}</a>
                                <form method="POST" action="{{ route('admin.portfolio.seo.destroy', $seo) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-md px-2 py-1 text-xs font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30">{{ __('messages.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-zinc-500">{{ __('messages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($seoSettings->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-800">{{ $seoSettings->links() }}</div>
        @endif
    </div>
</div>
@endsection
