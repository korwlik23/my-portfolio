<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Services\AuditLogger;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TranslationController extends Controller
{
    private const LOCALES = ['th', 'en', 'ja', 'zh'];

    public function __construct(private AuditLogger $auditLogger) {}

    public function index(Request $request)
    {
        $locale = $request->query('locale', app()->getLocale());
        if (!in_array($locale, self::LOCALES, true)) {
            $locale = app()->getLocale();
        }

        $search = $request->query('search');
        $page = max((int) $request->query('page', 1), 1);
        $perPage = 20;

        $baseTranslations = collect(Arr::dot(Lang::get('messages', [], $locale)))
            ->map(fn ($value) => is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE));

        $overrides = Translation::query()
            ->where('locale', $locale)
            ->where('group', 'messages')
            ->get()
            ->keyBy('key');

        $keys = $baseTranslations
            ->keys()
            ->merge($overrides->keys()->diff($baseTranslations->keys())->sort()->values())
            ->unique()
            ->values();

        if ($search) {
            $keys = $keys->filter(function ($key) use ($search, $baseTranslations, $overrides) {
                $override = $overrides->get($key);

                return str_contains($key, $search)
                    || str_contains((string) $baseTranslations->get($key, ''), $search)
                    || str_contains((string) ($override?->value ?? ''), $search);
            })->values();
        }

        $rows = $keys->map(function ($key) use ($locale, $baseTranslations, $overrides) {
            $override = $overrides->get($key);
            $baseValue = $baseTranslations->get($key, '');

            return (object) [
                'locale' => $locale,
                'group' => 'messages',
                'key' => $key,
                'base_value' => $baseValue,
                'value' => $override?->value ?? $baseValue,
                'override' => $override,
                'is_overridden' => (bool) $override,
            ];
        });

        $translations = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.translations.index', [
            'translations' => $translations,
            'locales' => self::LOCALES,
            'selectedLocale' => $locale,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $translation = Translation::query()->updateOrCreate(
            [
                'locale' => $data['locale'],
                'group' => $data['group'],
                'key' => $data['key'],
            ],
            ['value' => $data['value']]
        );

        Translation::clearCache();
        $this->auditLogger->log('translation.saved', $translation, "Saved translation: {$translation->locale}.{$translation->group}.{$translation->key}", [], [
            'value' => $translation->value,
        ]);

        return back()->with('success', __('messages.save_success'));
    }

    public function update(Request $request, Translation $translation)
    {
        $data = $request->validate([
            'value' => ['required', 'string'],
        ]);

        $oldValues = $translation->only(['value']);
        $translation->update($data);
        Translation::clearCache();
        $this->auditLogger->log('translation.updated', $translation, "Updated translation: {$translation->locale}.{$translation->group}.{$translation->key}", $oldValues, [
            'value' => $translation->value,
        ]);

        return back()->with('success', __('messages.update_success'));
    }

    public function destroy(Translation $translation)
    {
        $oldValues = $translation->only(['locale', 'group', 'key', 'value']);
        $this->auditLogger->log('translation.deleted', $translation, "Deleted translation: {$translation->locale}.{$translation->group}.{$translation->key}", $oldValues);
        $translation->delete();
        Translation::clearCache();

        return back()->with('success', __('messages.delete_success'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'locale' => ['required', Rule::in(self::LOCALES)],
            'group' => ['required', 'string', 'max:120', 'regex:/^[A-Za-z0-9_.-]+$/'],
            'key' => ['required', 'string', 'max:180', 'regex:/^[A-Za-z0-9_.-]+$/'],
            'value' => ['required', 'string'],
        ]);
    }
}
