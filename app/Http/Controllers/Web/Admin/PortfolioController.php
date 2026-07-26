<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Models\Experience;
use App\Models\Link;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Starter;
use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PortfolioController extends Controller
{
    public function __construct(private AuditLogger $auditLogger) {}

    public function dashboard()
    {
        return view('admin.portfolio.dashboard', [
            'stats' => [
                'projects' => Project::count(),
                'starters' => Starter::count(),
                'skills' => Skill::where('is_active', true)->count(),
                'services' => Service::where('is_active', true)->count(),
            ],
            'settings' => SiteSetting::pairs(),
            'recentProjects' => Project::query()->latest()->limit(5)->get(),
        ]);
    }

    public function settings()
    {
        return view('admin.portfolio.settings', [
            'settings' => SiteSetting::pairs(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            'default_language' => ['required', Rule::in(['th', 'en'])],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'github_url' => ['nullable', 'url', 'max:2048'],
            'linkedin_url' => ['nullable', 'url', 'max:2048'],
            'facebook_url' => ['nullable', 'url', 'max:2048'],
            'discord_url' => ['nullable', 'string', 'max:2048'],
            'line_url' => ['nullable', 'string', 'max:2048'],
            'resume_url' => ['nullable', 'string', 'max:2048'],
            'resume_th' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'resume_en' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        $existing = SiteSetting::pairs();
        $data['maintenance_mode'] = $request->has('maintenance_mode') ? '1' : '0';

        foreach (['logo' => 'logo_path', 'favicon' => 'favicon_path'] as $input => $key) {
            if ($request->hasFile($input)) {
                if (! empty($existing[$key]) && Storage::disk('public')->exists($existing[$key])) {
                    Storage::disk('public')->delete($existing[$key]);
                }
                $data[$key] = $request->file($input)->store('portfolio', 'public');
            } else {
                $data[$key] = $existing[$key] ?? null;
            }
            unset($data[$input]);
        }

        foreach (['resume_th' => 'resume_th_path', 'resume_en' => 'resume_en_path'] as $input => $key) {
            if ($request->hasFile($input)) {
                if (! empty($existing[$key]) && Storage::disk('public')->exists($existing[$key])) {
                    Storage::disk('public')->delete($existing[$key]);
                }
                $data[$key] = $request->file($input)->store('portfolio/resumes', 'public');
            } else {
                $data[$key] = $existing[$key] ?? null;
            }
            unset($data[$input]);
        }

        foreach ($data as $key => $value) {
            SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value, 'type' => $key === 'maintenance_mode' ? 'boolean' : 'string']);
        }

        SystemSetting::query()->updateOrCreate(['key' => 'brand_name'], ['value' => ['value' => $data['site_name']]]);
        if (! empty($data['logo_path'])) {
            SystemSetting::query()->updateOrCreate(['key' => 'logo_path'], ['value' => ['value' => $data['logo_path']]]);
        }

        $this->auditLogger->log('portfolio.settings.updated', null, 'Updated portfolio site settings');

        return back()->with('success', __('messages.save_success'));
    }

    public function editBlock(string $block)
    {
        abort_unless(in_array($block, ['hero', 'about', 'deployment'], true), 404);

        return view('admin.portfolio.content-block', [
            'blockKey' => $block,
            'block' => ContentBlock::query()->firstOrCreate(['key' => $block]),
        ]);
    }

    public function updateBlock(Request $request, string $block)
    {
        abort_unless(in_array($block, ['hero', 'about', 'deployment'], true), 404);

        $data = $request->validate([
            'title_th' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'subtitle_th' => ['nullable', 'string'],
            'subtitle_en' => ['nullable', 'string'],
            'body_th' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'primary_cta_text_th' => ['nullable', 'string', 'max:255'],
            'primary_cta_text_en' => ['nullable', 'string', 'max:255'],
            'primary_cta_url' => ['nullable', 'string', 'max:2048'],
            'secondary_cta_text_th' => ['nullable', 'string', 'max:255'],
            'secondary_cta_text_en' => ['nullable', 'string', 'max:255'],
            'secondary_cta_url' => ['nullable', 'string', 'max:2048'],
            'image' => ['nullable', 'image', 'max:2048'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'availability' => ['nullable', 'string', 'max:255'],
            'stats' => ['nullable', 'string'],
            'highlights_th' => ['nullable', 'string'],
            'highlights_en' => ['nullable', 'string'],
            'quick_facts_th' => ['nullable', 'string'],
            'quick_facts_en' => ['nullable', 'string'],
            'years_experience' => ['nullable', 'string', 'max:50'],
            'deployment_steps' => ['nullable', 'string'],
        ]);

        $model = ContentBlock::query()->firstOrCreate(['key' => $block]);
        if ($request->hasFile('image')) {
            if ($model->image_path && Storage::disk('public')->exists($model->image_path)) {
                Storage::disk('public')->delete($model->image_path);
            }
            $data['image_path'] = $request->file('image')->store('portfolio', 'public');
        }

        $data['settings'] = $this->blockSettings($block, $data);
        $data['is_active'] = $request->has('is_active');
        unset($data['image'], $data['availability'], $data['stats'], $data['highlights_th'], $data['highlights_en'], $data['quick_facts_th'], $data['quick_facts_en'], $data['years_experience'], $data['deployment_steps']);

        $model->update($data);
        $this->auditLogger->log('portfolio.block.updated', $model, "Updated portfolio block: {$block}");

        return back()->with('success', __('messages.save_success'));
    }

    public function seoIndex()
    {
        return view('admin.portfolio.seo.index', [
            'seoSettings' => SeoSetting::query()->orderBy('page_key')->paginate(20),
        ]);
    }

    public function seoCreate()
    {
        return view('admin.portfolio.seo.form', ['seo' => new SeoSetting]);
    }

    public function seoStore(Request $request)
    {
        $seo = SeoSetting::query()->create($this->validatedSeo($request));
        $this->auditLogger->log('portfolio.seo.created', $seo, "Created SEO setting: {$seo->page_key}");

        return redirect()->route('admin.portfolio.seo.index')->with('success', __('messages.create_success'));
    }

    public function seoEdit(SeoSetting $seo)
    {
        return view('admin.portfolio.seo.form', compact('seo'));
    }

    public function seoUpdate(Request $request, SeoSetting $seo)
    {
        $seo->update($this->validatedSeo($request, $seo));
        $this->auditLogger->log('portfolio.seo.updated', $seo, "Updated SEO setting: {$seo->page_key}");

        return redirect()->route('admin.portfolio.seo.index')->with('success', __('messages.update_success'));
    }

    public function seoDestroy(SeoSetting $seo)
    {
        $this->auditLogger->log('portfolio.seo.deleted', $seo, "Deleted SEO setting: {$seo->page_key}", $seo->toArray());
        $seo->delete();

        return back()->with('success', __('messages.delete_success'));
    }

    public function index(Request $request, string $resource)
    {
        $config = $this->resourceConfig($resource);
        $query = $config['model']::query();

        if ($resource === 'skills') {
            if ($request->filled('category')) {
                $query->where('category', $request->string('category'));
            }

            if ($request->filled('level')) {
                $query->where('level', $request->string('level'));
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
        }

        return view('admin.portfolio.resources.index', [
            'resource' => $resource,
            'config' => $config,
            'items' => $query->orderBy('display_order')->latest()->paginate(20)->withQueryString(),
            'filters' => $resource === 'skills' ? [
                'category' => $request->query('category'),
                'level' => $request->query('level'),
                'is_active' => $request->query('is_active'),
            ] : [],
        ]);
    }

    public function create(string $resource)
    {
        $config = $this->resourceConfig($resource);

        return view('admin.portfolio.resources.form', [
            'resource' => $resource,
            'config' => $config,
            'item' => new $config['model'],
            'extra' => [],
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $config = $this->resourceConfig($resource);
        $data = $this->validatedResource($request, $resource);
        $item = $config['model']::query()->create($data['attributes']);
        $this->syncResourceRelations($item, $resource, $data['relations']);
        $this->auditLogger->log("portfolio.{$resource}.created", $item, "Created {$resource} item");

        return redirect()->route('admin.portfolio.resources.index', $resource)->with('success', __('messages.create_success'));
    }

    public function edit(string $resource, int $id)
    {
        $config = $this->resourceConfig($resource);
        $item = $config['model']::query()->findOrFail($id);

        return view('admin.portfolio.resources.form', [
            'resource' => $resource,
            'config' => $config,
            'item' => $item,
            'extra' => $this->resourceExtra($item, $resource),
        ]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $config = $this->resourceConfig($resource);
        $item = $config['model']::query()->findOrFail($id);
        $data = $this->validatedResource($request, $resource, $item);
        $oldValues = $item->toArray();

        $item->update($data['attributes']);
        $this->syncResourceRelations($item, $resource, $data['relations']);
        $this->auditLogger->log("portfolio.{$resource}.updated", $item, "Updated {$resource} item", $oldValues, $item->fresh()->toArray());

        return redirect()->route('admin.portfolio.resources.index', $resource)->with('success', __('messages.update_success'));
    }

    public function destroy(string $resource, int $id)
    {
        $config = $this->resourceConfig($resource);
        $item = $config['model']::query()->findOrFail($id);
        $this->auditLogger->log("portfolio.{$resource}.deleted", $item, "Deleted {$resource} item", $item->toArray());
        $item->delete();

        return back()->with('success', __('messages.delete_success'));
    }

    private function resourceConfig(string $resource): array
    {
        $configs = [
            'projects' => [
                'title' => __('messages.projects'),
                'model' => Project::class,
                'name_field' => 'name',
                'select_options' => [
                    'type' => [
                        'starter' => 'Starter',
                        'case-study' => 'Case Study',
                        'dashboard' => 'Dashboard',
                        'website' => 'Website',
                        'service' => 'Service',
                        'tool' => 'Tool',
                        'saas' => 'SaaS',
                    ],
                    'status' => [
                        'concept' => 'Concept',
                        'live' => 'Live',
                        'available' => 'Available',
                        'demo' => 'Demo',
                        'draft' => 'Draft',
                        'in-progress' => 'In Progress',
                        'progress' => 'Progress',
                        'published' => 'Published',
                        'architecture' => 'Architecture',
                        'case-study' => 'Case Study',
                        'archived' => 'Archived',
                    ],
                ],
                'fields' => ['name', 'slug', 'type', 'status', 'description_th', 'description_en', 'case_study_th', 'case_study_en', 'live_demo_url', 'github_url', 'image', 'is_featured', 'is_public', 'display_order', 'tech_stack_tags', 'feature_list_th', 'feature_list_en'],
            ],
            'starters' => [
                'title' => __('messages.starters'),
                'model' => Starter::class,
                'name_field' => 'name',
                'fields' => ['name', 'slug', 'description_th', 'description_en', 'stack', 'demo_url', 'github_url', 'status', 'setup_notes_th', 'setup_notes_en', 'deploy_notes_th', 'deploy_notes_en', 'is_public', 'display_order'],
            ],
            'skills' => [
                'title' => __('messages.skills'),
                'model' => Skill::class,
                'name_field' => 'name',
                'filters' => [
                    'category' => ['Backend', 'Frontend', 'Database', 'DevOps', 'Product', 'Tools', 'AI'],
                    'level' => ['basic', 'intermediate', 'advanced'],
                    'is_active' => [1 => __('messages.yes'), 0 => __('messages.no')],
                ],
                'fields' => ['name', 'category', 'level', 'icon', 'display_order', 'is_active'],
            ],
            'services' => [
                'title' => __('messages.services'),
                'model' => Service::class,
                'name_field' => 'title_en',
                'fields' => ['title_th', 'title_en', 'description_th', 'description_en', 'price_range', 'is_active', 'display_order'],
            ],
            'experiences' => [
                'title' => __('messages.experience'),
                'model' => Experience::class,
                'name_field' => 'title_en',
                'fields' => ['title_th', 'title_en', 'company', 'period', 'description_th', 'description_en', 'tech_stack', 'display_order', 'is_active'],
            ],
            'links' => [
                'title' => __('messages.contact_links'),
                'model' => Link::class,
                'name_field' => 'label',
                'fields' => ['type', 'label', 'url', 'is_active', 'display_order'],
            ],
        ];

        abort_unless(isset($configs[$resource]), 404);

        return $configs[$resource];
    }

    private function validatedResource(Request $request, string $resource, ?Model $item = null): array
    {
        $id = $item?->getKey();
        $rules = match ($resource) {
            'projects' => [
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($id)],
                'type' => ['nullable', Rule::in(['starter', 'case-study', 'dashboard', 'website', 'service', 'tool', 'saas'])],
                'status' => ['nullable', Rule::in(['concept', 'live', 'available', 'demo', 'draft', 'in-progress', 'progress', 'published', 'architecture', 'case-study', 'archived'])],
                'description_th' => ['nullable', 'string'],
                'description_en' => ['nullable', 'string'],
                'case_study_th' => ['nullable', 'string'],
                'case_study_en' => ['nullable', 'string'],
                'live_demo_url' => ['nullable', 'url', 'max:2048'],
                'github_url' => ['nullable', 'url', 'max:2048'],
                'image' => ['nullable', 'image', 'max:2048'],
                'is_featured' => ['nullable', 'boolean'],
                'is_public' => ['nullable', 'boolean'],
                'display_order' => ['nullable', 'integer', 'min:0'],
                'tech_stack_tags' => ['nullable', 'string'],
                'feature_list_th' => ['nullable', 'string'],
                'feature_list_en' => ['nullable', 'string'],
            ],
            'starters' => [
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', Rule::unique('starters', 'slug')->ignore($id)],
                'description_th' => ['nullable', 'string'],
                'description_en' => ['nullable', 'string'],
                'stack' => ['nullable', 'string'],
                'demo_url' => ['nullable', 'url', 'max:2048'],
                'github_url' => ['nullable', 'url', 'max:2048'],
                'status' => ['nullable', 'string', 'max:120'],
                'setup_notes_th' => ['nullable', 'string'],
                'setup_notes_en' => ['nullable', 'string'],
                'deploy_notes_th' => ['nullable', 'string'],
                'deploy_notes_en' => ['nullable', 'string'],
                'is_public' => ['nullable', 'boolean'],
                'display_order' => ['nullable', 'integer', 'min:0'],
            ],
            'skills' => [
                'name' => ['required', 'string', 'max:255'],
                'category' => ['required', Rule::in(['Backend', 'Frontend', 'Database', 'DevOps', 'Product', 'Tools', 'AI'])],
                'level' => ['required', Rule::in(['basic', 'intermediate', 'advanced'])],
                'icon' => ['nullable', 'string', 'max:120'],
                'display_order' => ['nullable', 'integer', 'min:0'],
                'is_active' => ['nullable', 'boolean'],
            ],
            'services' => [
                'title_th' => ['required', 'string', 'max:255'],
                'title_en' => ['required', 'string', 'max:255'],
                'description_th' => ['nullable', 'string'],
                'description_en' => ['nullable', 'string'],
                'price_range' => ['nullable', 'string', 'max:120'],
                'is_active' => ['nullable', 'boolean'],
                'display_order' => ['nullable', 'integer', 'min:0'],
            ],
            'experiences' => [
                'title_th' => ['required', 'string', 'max:255'],
                'title_en' => ['required', 'string', 'max:255'],
                'company' => ['nullable', 'string', 'max:255'],
                'period' => ['nullable', 'string', 'max:255'],
                'description_th' => ['nullable', 'string'],
                'description_en' => ['nullable', 'string'],
                'tech_stack' => ['nullable', 'string'],
                'display_order' => ['nullable', 'integer', 'min:0'],
                'is_active' => ['nullable', 'boolean'],
            ],
            'links' => [
                'type' => ['required', 'string', 'max:120'],
                'label' => ['nullable', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:2048'],
                'is_active' => ['nullable', 'boolean'],
                'display_order' => ['nullable', 'integer', 'min:0'],
            ],
        };

        $data = $request->validate($rules);
        $relations = [];

        foreach (['is_active', 'is_public', 'is_featured'] as $boolean) {
            if (array_key_exists($boolean, $rules)) {
                $data[$boolean] = $request->has($boolean);
            }
        }

        if (in_array($resource, ['projects', 'starters'], true)) {
            $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        }

        if ($resource === 'projects') {
            if ($request->hasFile('image')) {
                if ($item instanceof Project && $item->image_path && Storage::disk('public')->exists($item->image_path)) {
                    Storage::disk('public')->delete($item->image_path);
                }
                $data['image_path'] = $request->file('image')->store('portfolio/projects', 'public');
            }
            $relations['tech_stack_tags'] = $this->normalizeTags($data['tech_stack_tags'] ?? '');
            $relations['feature_list_th'] = $this->lines($data['feature_list_th'] ?? '');
            $relations['feature_list_en'] = $this->lines($data['feature_list_en'] ?? '');
            unset($data['image'], $data['tech_stack_tags'], $data['feature_list_th'], $data['feature_list_en']);
        }

        if (in_array($resource, ['starters', 'experiences'], true)) {
            $jsonField = $resource === 'starters' ? 'stack' : 'tech_stack';
            $data[$jsonField] = $this->lines($data[$jsonField] ?? '');
        }

        return ['attributes' => $data, 'relations' => $relations];
    }

    private function syncResourceRelations(Model $item, string $resource, array $relations): void
    {
        if (! $item instanceof Project || $resource !== 'projects') {
            return;
        }

        $item->techStacks()->delete();
        foreach ($relations['tech_stack_tags'] ?? [] as $tag) {
            $item->techStacks()->create(['name' => $tag]);
        }

        $th = $relations['feature_list_th'] ?? [];
        $en = $relations['feature_list_en'] ?? [];
        $max = max(count($th), count($en));
        $item->features()->delete();
        for ($i = 0; $i < $max; $i++) {
            $item->features()->create([
                'description_th' => $th[$i] ?? null,
                'description_en' => $en[$i] ?? null,
                'display_order' => $i + 1,
            ]);
        }
    }

    private function resourceExtra(Model $item, string $resource): array
    {
        if ($item instanceof Project && $resource === 'projects') {
            return [
                'tech_stack_tags' => collect($item->techStacks()->pluck('name')->all())
                    ->flatMap(fn (string $tag) => $this->normalizeTags($tag))
                    ->implode(' | '),
                'feature_list_th' => $item->features()->pluck('description_th')->filter()->implode("\n"),
                'feature_list_en' => $item->features()->pluck('description_en')->filter()->implode("\n"),
            ];
        }

        if ($resource === 'starters') {
            return ['stack' => implode("\n", $item->stack ?? [])];
        }

        if ($resource === 'experiences') {
            return ['tech_stack' => implode("\n", $item->tech_stack ?? [])];
        }

        return [];
    }

    private function validatedSeo(Request $request, ?SeoSetting $seo = null): array
    {
        $data = $request->validate([
            'page_key' => ['required', 'string', 'max:120', Rule::unique('seo_settings', 'page_key')->ignore($seo?->id)],
            'meta_title_th' => ['nullable', 'string', 'max:255'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_description_th' => ['nullable', 'string'],
            'meta_description_en' => ['nullable', 'string'],
            'og_image' => ['nullable', 'image', 'max:2048'],
            'keywords' => ['nullable', 'string'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($request->hasFile('og_image')) {
            if ($seo?->og_image_path && Storage::disk('public')->exists($seo->og_image_path)) {
                Storage::disk('public')->delete($seo->og_image_path);
            }
            $data['og_image_path'] = $request->file('og_image')->store('portfolio/seo', 'public');
        }
        unset($data['og_image']);

        return $data;
    }

    private function blockSettings(string $block, array $data): array
    {
        return match ($block) {
            'hero' => [
                'availability' => $data['availability'] ?? null,
                'stats' => collect($this->lines($data['stats'] ?? ''))
                    ->map(function (string $line) {
                        [$value, $labelEn, $labelTh] = array_pad(array_map('trim', explode('|', $line)), 3, null);

                        return ['value' => $value, 'label_en' => $labelEn ?: $value, 'label_th' => $labelTh ?: $labelEn ?: $value];
                    })
                    ->values()
                    ->all(),
            ],
            'about' => [
                'years_experience' => $data['years_experience'] ?? null,
                'highlights_th' => $this->lines($data['highlights_th'] ?? ''),
                'highlights_en' => $this->lines($data['highlights_en'] ?? ''),
                'quick_facts_th' => $this->factLines($data['quick_facts_th'] ?? ''),
                'quick_facts_en' => $this->factLines($data['quick_facts_en'] ?? ''),
            ],
            'deployment' => [
                'steps' => $this->lines($data['deployment_steps'] ?? ''),
            ],
            default => [],
        };
    }

    private function factLines(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(function (string $line) {
                $parts = array_map('trim', explode('|', $line, 2));

                return count($parts) === 2 && $parts[0] !== '' && $parts[1] !== ''
                    ? ['label' => $parts[0], 'value' => $parts[1]]
                    : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function lines(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeTags(?string $value): array
    {
        return collect(preg_split('/\s*\|\s*|\s*,\s*|\r\n|\r|\n/', (string) $value))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
