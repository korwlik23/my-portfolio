@extends('layouts.app')

@section('page-title', $config['title'])

@section('content')
@php
    $statusBadge = function ($value): array {
        if (is_bool($value)) {
            return $value
                ? ['label' => __('messages.yes'), 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300']
                : ['label' => __('messages.no'), 'class' => 'border-zinc-200 bg-zinc-50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400'];
        }

        if ($value instanceof \UnitEnum) {
            $value = $value->value;
        }

        $normalized = str((string) $value)->lower()->replace(['_', ' '], '-')->toString();

        return match ($normalized) {
            'live', 'available', 'published' => ['label' => ucfirst(str_replace('-', ' ', $normalized)), 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300'],
            'demo' => ['label' => 'Demo', 'class' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950/60 dark:text-blue-300'],
            'draft', 'in-progress', 'progress' => ['label' => ucfirst(str_replace('-', ' ', $normalized)), 'class' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950/60 dark:text-amber-300'],
            'architecture', 'case-study' => ['label' => ucfirst(str_replace('-', ' ', $normalized)), 'class' => 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900 dark:bg-violet-950/60 dark:text-violet-300'],
            default => ['label' => $normalized !== '' ? ucfirst(str_replace('-', ' ', $normalized)) : '-', 'class' => 'border-zinc-200 bg-zinc-50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400'],
        };
    };
@endphp

<div class="space-y-6">
    @if($resource === 'skills')
        <form method="GET" class="grid gap-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:grid-cols-2 lg:grid-cols-4">
            <label class="block">
                <span class="mb-1.5 block text-xs font-semibold uppercase text-zinc-500">{{ __('messages.category') }}</span>
                <select name="category" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="">-</option>
                    @foreach($config['filters']['category'] ?? [] as $option)
                        <option value="{{ $option }}" @selected(($filters['category'] ?? null) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-xs font-semibold uppercase text-zinc-500">{{ __('messages.level') }}</span>
                <select name="level" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="">-</option>
                    @foreach($config['filters']['level'] ?? [] as $option)
                        <option value="{{ $option }}" @selected(($filters['level'] ?? null) === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-xs font-semibold uppercase text-zinc-500">{{ __('messages.is_active') }}</span>
                <select name="is_active" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="">-</option>
                    <option value="1" @selected(($filters['is_active'] ?? null) === '1')>{{ __('messages.yes') }}</option>
                    <option value="0" @selected(($filters['is_active'] ?? null) === '0')>{{ __('messages.no') }}</option>
                </select>
            </label>
            <div class="flex items-end gap-2">
                <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.filter') }}</button>
                <a href="{{ route('admin.portfolio.resources.index', $resource) }}" class="rounded-lg border border-zinc-200 px-4 py-2 text-sm font-semibold hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800">{{ __('messages.reset') }}</a>
            </div>
        </form>
    @endif

    <div class="flex items-center justify-between gap-4">
        <p class="text-sm text-zinc-500">{{ __('messages.manage_portfolio_resource') }}</p>
        <a href="{{ route('admin.portfolio.resources.create', $resource) }}" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900">{{ __('messages.create') }}</a>
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-800">
                        <th class="px-5 py-3">{{ __('messages.name') }}</th>
                        <th class="px-5 py-3">{{ __('messages.status') }}</th>
                        <th class="px-5 py-3">{{ __('messages.is_public') }}</th>
                        <th class="px-5 py-3">{{ __('messages.is_featured') }}</th>
                        <th class="px-5 py-3">{{ __('messages.display_order') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('messages.manage') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($items as $item)
                        @php
                            $nameField = $config['name_field'];
                            $status = $item->status ?? ($item->is_active ?? $item->is_public ?? null);
                            $statusBadgeData = $statusBadge($status);
                            $isPublic = (bool) ($item->is_public ?? false);
                            $isFeatured = (bool) ($item->is_featured ?? false);
                        @endphp
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $item->{$nameField} ?: $item->name ?? $item->type ?? '#' . $item->id }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusBadgeData['class'] }}">
                                    {{ $statusBadgeData['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $isPublic ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300' : 'border-zinc-200 bg-zinc-50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400' }}">
                                    {{ $isPublic ? __('messages.yes') : __('messages.no') }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $isFeatured ? 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900 dark:bg-violet-950/60 dark:text-violet-300' : 'border-zinc-200 bg-zinc-50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400' }}">
                                    {{ $isFeatured ? __('messages.yes') : __('messages.no') }}
                                </span>
                            </td>
                            <td class="px-5 py-3">{{ $item->display_order ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.portfolio.resources.edit', [$resource, $item->id]) }}" class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                        {{ __('messages.edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.portfolio.resources.destroy', [$resource, $item->id]) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium text-red-500 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-950/30">
                                            <x-icon name="delete" class="h-3.5 w-3.5" />
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-500">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-800">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection
