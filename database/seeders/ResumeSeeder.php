<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Models\Experience;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ResumeSeeder extends Seeder
{
    private const PROFILE_KEYS = [
        'owner_name',
        'owner_title_en',
        'owner_title_th',
        'location_en',
        'location_th',
        'location_code',
        'timezone',
        'hero_highlight_en',
        'hero_highlight_th',
    ];

    public function run(?array $data = null): void
    {
        $data ??= require database_path('seeders/data/resume.php');

        DB::transaction(function () use ($data) {
            $this->profile($data['profile'] ?? []);
            $this->experiences($data['experiences'] ?? []);
            $this->skills($data['skills'] ?? [], $data['core_stack'] ?? []);
            $this->services($data['services'] ?? []);
            $this->deployment($data['deployment'] ?? []);
        });
    }

    private function profile(array $profile): void
    {
        $unknown = array_diff(array_keys($profile), self::PROFILE_KEYS);
        if ($unknown !== []) {
            throw new InvalidArgumentException('resume.php profile: unknown key(s) '.implode(', ', $unknown).'. Allowed: '.implode(', ', self::PROFILE_KEYS).'.');
        }

        $timezone = $profile['timezone'] ?? '';
        if ($timezone !== '' && ! in_array($timezone, timezone_identifiers_list(), true)) {
            throw new InvalidArgumentException("resume.php profile.timezone: '{$timezone}' is not a valid timezone, e.g. 'Asia/Bangkok'.");
        }

        foreach ($profile as $key => $value) {
            SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => (string) $value, 'type' => 'string']);
        }
    }

    private function experiences(array $items): void
    {
        $keys = [];

        foreach (array_values($items) as $index => $item) {
            $label = "resume.php experiences[{$index}]";
            $key = $item['key'] ?? '';
            $kind = $item['kind'] ?? 'work';

            if (! is_string($key) || $key === '') {
                throw new InvalidArgumentException("{$label}: 'key' is required and must be unique, e.g. 'infinite-brain'.");
            }
            if (in_array($key, $keys, true)) {
                throw new InvalidArgumentException("{$label}: key '{$key}' is used twice.");
            }
            if (! in_array($kind, Experience::KINDS, true)) {
                throw new InvalidArgumentException("{$label}: kind '{$kind}' must be one of ".implode(', ', Experience::KINDS).'.');
            }
            if (empty($item['title_en'])) {
                throw new InvalidArgumentException("{$label}: 'title_en' is required.");
            }

            $startedOn = $this->month($item['from'] ?? null, "{$label}.from");
            $endedOn = $this->month($item['to'] ?? null, "{$label}.to");
            if ($startedOn && $endedOn && $endedOn < $startedOn) {
                throw new InvalidArgumentException("{$label}: 'to' is before 'from'.");
            }

            $keys[] = $key;

            // Rows created before keys existed are adopted by title so re-seeding does not duplicate them.
            $experience = Experience::withTrashed()->where('key', $key)->first()
                ?? Experience::withTrashed()->whereNull('key')->where('title_en', $item['title_en'])->first()
                ?? new Experience;

            $experience->fill([
                'key' => $key,
                'kind' => $kind,
                'title_en' => $item['title_en'],
                'title_th' => $item['title_th'] ?? $item['title_en'],
                'company' => $item['company'] ?? null,
                'period' => $item['period'] ?? null,
                'started_on' => $startedOn,
                'ended_on' => $endedOn,
                'description_en' => implode("\n", (array) ($item['description_en'] ?? [])),
                'description_th' => implode("\n", (array) ($item['description_th'] ?? [])),
                'tech_stack' => array_values($item['tech_stack'] ?? []),
                'display_order' => $index + 1,
                'is_active' => true,
            ]);
            $experience->deleted_at = null;
            $experience->save();
        }

        Experience::query()
            ->where(fn ($query) => $query->whereNull('key')->orWhereNotIn('key', $keys))
            ->update(['is_active' => false]);
    }

    private function skills(array $groups, array $core): void
    {
        $names = [];
        foreach ($groups as $category => $items) {
            if (! in_array($category, Skill::CATEGORIES, true)) {
                throw new InvalidArgumentException("resume.php skills: unknown category '{$category}'. Use one of ".implode(', ', Skill::CATEGORIES).'.');
            }
            foreach ($items as $name) {
                if (in_array($name, $names, true)) {
                    throw new InvalidArgumentException("resume.php skills: '{$name}' is listed twice.");
                }
                $names[] = $name;
            }
        }

        $missing = array_diff($core, $names);
        if ($missing !== []) {
            throw new InvalidArgumentException('resume.php core_stack: not listed under skills: '.implode(', ', $missing).'.');
        }

        $order = 1;
        foreach ($groups as $category => $items) {
            foreach ($items as $name) {
                $skill = Skill::withTrashed()->firstOrNew(['name' => $name]);
                $skill->fill([
                    'category' => $category,
                    'level' => in_array($name, $core, true) ? 'advanced' : 'intermediate',
                    'display_order' => $order++,
                    'is_active' => true,
                ]);
                $skill->deleted_at = null;
                $skill->save();
            }
        }

        Skill::query()->whereNotIn('name', $names)->update(['is_active' => false]);
    }

    private function services(array $items): void
    {
        $titles = [];

        foreach (array_values($items) as $index => $item) {
            $title = $item['title_en'] ?? '';
            if ($title === '') {
                throw new InvalidArgumentException("resume.php services[{$index}]: 'title_en' is required.");
            }
            $titles[] = $title;

            $service = Service::withTrashed()->firstOrNew(['title_en' => $title]);
            $service->fill([
                'title_th' => $item['title_th'] ?? $title,
                'description_en' => $item['description_en'] ?? null,
                'description_th' => $item['description_th'] ?? null,
                'price_range' => $item['price_range'] ?? null,
                'is_active' => true,
                'display_order' => $index + 1,
            ]);
            $service->deleted_at = null;
            $service->save();
        }

        Service::query()->whereNotIn('title_en', $titles)->update(['is_active' => false]);
    }

    private function deployment(array $deployment): void
    {
        $updates = [];

        if (array_key_exists('nodes', $deployment)) {
            $updates['nodes'] = collect($deployment['nodes'])
                ->values()
                ->map(function ($node, int $index) {
                    if (empty($node['name'])) {
                        throw new InvalidArgumentException("resume.php deployment.nodes[{$index}]: 'name' is required.");
                    }

                    return [
                        'layer' => (string) ($node['layer'] ?? ''),
                        'name' => (string) $node['name'],
                        'note' => (string) ($node['note'] ?? ''),
                    ];
                })
                ->all();
        }

        if (array_key_exists('steps', $deployment)) {
            $updates['steps'] = array_values($deployment['steps']);
        }

        if ($updates === []) {
            return;
        }

        $block = ContentBlock::query()->firstOrCreate(['key' => 'deployment'], ['is_active' => true]);
        $block->settings = array_merge($block->settings ?? [], $updates);
        $block->save();
    }

    private function month(?string $value, string $label): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (preg_match('/^\d{4}$/', $value)) {
            return "{$value}-01-01";
        }
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            return "{$value}-01";
        }

        throw new InvalidArgumentException("{$label}: use 'YYYY-MM' or 'YYYY', got '{$value}'.");
    }
}
