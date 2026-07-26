<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PortfolioProjectImporter
{
    private const TYPES = ['starter', 'case-study', 'dashboard', 'website', 'service', 'tool', 'saas'];

    private const STATUSES = [
        'concept',
        'live',
        'available',
        'demo',
        'draft',
        'in-progress',
        'progress',
        'published',
        'architecture',
        'case-study',
        'archived',
    ];

    public function import(array $payload, bool $dryRun = false): array
    {
        $projects = $this->validatedProjects($payload);
        $existingSlugs = Project::withTrashed()->whereIn('slug', Arr::pluck($projects, 'slug'))->pluck('deleted_at', 'slug');

        $summary = [
            'total' => count($projects),
            'created' => 0,
            'updated' => 0,
            'restored' => 0,
            'dry_run' => $dryRun,
            'projects' => [],
        ];

        foreach ($projects as $project) {
            $slug = $project['slug'];
            $action = match (true) {
                ! $existingSlugs->has($slug) => 'created',
                $existingSlugs->get($slug) !== null => 'restored',
                default => 'updated',
            };

            $summary[$action]++;
            $summary['projects'][] = [
                'slug' => $slug,
                'name' => $project['name'],
                'action' => $action,
            ];
        }

        if ($dryRun) {
            return $summary;
        }

        DB::transaction(function () use ($projects): void {
            foreach ($projects as $project) {
                $attributes = Arr::only($project, [
                    'name',
                    'slug',
                    'type',
                    'status',
                    'description_th',
                    'description_en',
                    'case_study_th',
                    'case_study_en',
                    'live_demo_url',
                    'github_url',
                    'image_path',
                    'is_featured',
                    'is_public',
                    'display_order',
                ]);

                $model = Project::withTrashed()->firstOrNew(['slug' => $project['slug']]);
                $model->fill($attributes);
                $model->save();

                if ($model->trashed()) {
                    $model->restore();
                }

                $model->techStacks()->delete();
                foreach ($project['tech_stack'] as $name) {
                    $model->techStacks()->create(['name' => $name]);
                }

                $model->features()->delete();
                foreach ($project['features'] as $index => $feature) {
                    $model->features()->create([
                        'description_th' => $feature['th'] ?? null,
                        'description_en' => $feature['en'] ?? null,
                        'display_order' => $index + 1,
                    ]);
                }
            }
        });

        return $summary;
    }

    private function validatedProjects(array $payload): array
    {
        $validator = Validator::make($payload, [
            'projects' => ['required', 'array', 'min:1', 'max:200'],
            'projects.*.name' => ['required', 'string', 'max:255'],
            'projects.*.slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'projects.*.type' => ['nullable', Rule::in(self::TYPES)],
            'projects.*.status' => ['nullable', Rule::in(self::STATUSES)],
            'projects.*.description_th' => ['nullable', 'string'],
            'projects.*.description_en' => ['nullable', 'string'],
            'projects.*.case_study_th' => ['nullable', 'string'],
            'projects.*.case_study_en' => ['nullable', 'string'],
            'projects.*.live_demo_url' => ['nullable', 'url', 'max:2048'],
            'projects.*.github_url' => ['nullable', 'url', 'max:2048'],
            'projects.*.image_path' => ['nullable', 'string', 'max:2048'],
            'projects.*.is_featured' => ['nullable', 'boolean'],
            'projects.*.is_public' => ['nullable', 'boolean'],
            'projects.*.display_order' => ['nullable', 'integer', 'min:0'],
            'projects.*.tech_stack' => ['nullable', 'array', 'max:30'],
            'projects.*.tech_stack.*' => ['required', 'string', 'max:120'],
            'projects.*.features' => ['nullable', 'array', 'max:30'],
            'projects.*.features.*.th' => ['nullable', 'string'],
            'projects.*.features.*.en' => ['nullable', 'string'],
        ]);

        $validator->validate();

        $projects = collect($payload['projects'])
            ->map(function (array $project): array {
                $slug = $project['slug'] ?? Str::slug($project['name']);

                return [
                    'name' => $project['name'],
                    'slug' => $slug,
                    'type' => $project['type'] ?? null,
                    'status' => $project['status'] ?? 'draft',
                    'description_th' => $project['description_th'] ?? null,
                    'description_en' => $project['description_en'] ?? null,
                    'case_study_th' => $project['case_study_th'] ?? null,
                    'case_study_en' => $project['case_study_en'] ?? null,
                    'live_demo_url' => $project['live_demo_url'] ?? null,
                    'github_url' => $project['github_url'] ?? null,
                    'image_path' => $project['image_path'] ?? null,
                    'is_featured' => $project['is_featured'] ?? false,
                    'is_public' => $project['is_public'] ?? false,
                    'display_order' => $project['display_order'] ?? 0,
                    'tech_stack' => collect($project['tech_stack'] ?? [])->map(fn (string $tech) => trim($tech))->filter()->values()->all(),
                    'features' => collect($project['features'] ?? [])
                        ->map(fn (array $feature) => [
                            'th' => $feature['th'] ?? null,
                            'en' => $feature['en'] ?? null,
                        ])
                        ->filter(fn (array $feature) => filled($feature['th']) || filled($feature['en']))
                        ->values()
                        ->all(),
                ];
            })
            ->values();

        $missingSlugs = $projects
            ->filter(fn (array $project) => blank($project['slug']))
            ->pluck('name')
            ->values();

        if ($missingSlugs->isNotEmpty()) {
            throw ValidationException::withMessages([
                'projects' => 'Project slug is required when the project name cannot be converted to a URL slug: '.$missingSlugs->implode(', '),
            ]);
        }

        $duplicates = $projects->pluck('slug')->duplicates()->values();
        if ($duplicates->isNotEmpty()) {
            throw ValidationException::withMessages([
                'projects' => 'Duplicate project slugs in import file: '.$duplicates->implode(', '),
            ]);
        }

        $techStackErrors = [];
        foreach ($projects as $index => $project) {
            $normalized = array_map(
                fn (string $tech) => mb_strtolower($tech),
                $project['tech_stack']
            );
            $duplicateTech = collect($normalized)->duplicates()->values();
            if ($duplicateTech->isNotEmpty()) {
                $techStackErrors["projects.$index.tech_stack"] = 'Duplicate tech stack entries within the same project: '.$duplicateTech->implode(', ');
            }
        }
        if (! empty($techStackErrors)) {
            throw ValidationException::withMessages($techStackErrors);
        }

        return $projects->all();
    }
}
