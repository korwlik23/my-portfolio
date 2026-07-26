<?php

namespace Tests\Feature;

use App\Models\ContentBlock;
use App\Models\Link;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\PortfolioSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_portfolio_renders_seeded_content(): void
    {
        $this->seed(PortfolioSeeder::class);

        $response = $this
            ->get('/')
            ->assertOk()
            ->assertSee('Fullstack Developer for Practical Web Systems')
            ->assertSee('View Live Projects')
            ->assertSee('Quick Facts')
            ->assertSee('Core Stack Highlight')
            ->assertSee('Laravel Admin Starter')
            ->assertSee('Main Starter')
            ->assertSee('Private Repo')
            ->assertSee('Cloud Deployment Case Study')
            ->assertSee('Cloudflare DNS')
            ->assertSee('Deployment Architecture')
            ->assertSee('id="deployment"', false)
            ->assertSee('MySQL / MariaDB Databases')
            ->assertSee('OpenAI Codex')
            ->assertSee('Postman')
            ->assertDontSee('messages.skills');

        $this->assertSame(1, substr_count($response->getContent(), 'data-portfolio-navbar'));
        $this->assertStringNotContainsString('hello@example.com', $response->getContent());
        $this->assertStringNotContainsString('https://github.com/', $response->getContent());
    }

    public function test_portfolio_admin_requires_permission(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        $this
            ->actingAs($user)
            ->get('/admin/portfolio')
            ->assertForbidden();
    }

    public function test_admin_can_create_project_from_cms(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this
            ->actingAs($admin)
            ->post('/admin/portfolio/projects', [
                'name' => 'Client Portal Demo',
                'slug' => 'client-portal-demo',
                'type' => 'tool',
                'status' => 'demo',
                'description_th' => 'ระบบ portal ตัวอย่าง',
                'description_en' => 'Client portal demo system',
                'tech_stack_tags' => "Laravel\nMySQL",
                'feature_list_th' => "ระบบหลังบ้าน\nResponsive UI",
                'feature_list_en' => "Admin dashboard\nResponsive UI",
                'is_featured' => '1',
                'is_public' => '1',
                'display_order' => '10',
            ])
            ->assertRedirect('/admin/portfolio/projects');

        $project = Project::where('slug', 'client-portal-demo')->firstOrFail();

        $this->assertTrue($project->is_featured);
        $this->assertTrue($project->is_public);
        $this->assertCount(2, $project->techStacks);
        $this->assertCount(2, $project->features);
    }

    public function test_public_portfolio_outputs_seo_metadata(): void
    {
        $this->seed(PortfolioSeeder::class);

        $this
            ->get('/')
            ->assertOk()
            ->assertSee('Tewarach - Fullstack Developer for Practical Web Systems')
            ->assertSee('og:title', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_admin_can_upload_thai_and_english_resumes(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        $this->seed(PortfolioSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this
            ->actingAs($admin)
            ->put('/admin/portfolio/settings', [
                'site_name' => 'Tewarach Portfolio CMS',
                'default_language' => 'en',
                'contact_email' => 'hello@example.com',
                'github_url' => 'https://github.com/',
                'linkedin_url' => 'https://www.linkedin.com/',
                'facebook_url' => 'https://www.facebook.com/',
                'resume_th' => UploadedFile::fake()->create('resume-th.pdf', 120, 'application/pdf'),
                'resume_en' => UploadedFile::fake()->create('resume-en.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect();

        $resumeTh = SiteSetting::value('resume_th_path');
        $resumeEn = SiteSetting::value('resume_en_path');

        $this->assertNotEmpty($resumeTh);
        $this->assertNotEmpty($resumeEn);
        Storage::disk('public')->assertExists($resumeTh);
        Storage::disk('public')->assertExists($resumeEn);
    }

    public function test_public_portfolio_shows_resume_download_links_when_configured(): void
    {
        Storage::fake('public');
        $this->seed(PortfolioSeeder::class);
        SiteSetting::query()->updateOrCreate(['key' => 'resume_th_path'], ['value' => 'portfolio/resumes/resume-th.pdf', 'type' => 'string']);
        SiteSetting::query()->updateOrCreate(['key' => 'resume_en_path'], ['value' => 'portfolio/resumes/resume-en.pdf', 'type' => 'string']);
        Storage::disk('public')->put('portfolio/resumes/resume-th.pdf', 'thai resume');
        Storage::disk('public')->put('portfolio/resumes/resume-en.pdf', 'english resume');

        $this
            ->get('/')
            ->assertOk()
            ->assertSee(__('messages.download_resume_th'))
            ->assertSee(__('messages.download_resume_en'))
            ->assertSee(route('resume.download', 'th'))
            ->assertSee(route('resume.download', 'en'));

        $content = $this->get('/')->getContent();
        $this->assertGreaterThanOrEqual(1, substr_count($content, route('resume.download', 'th')));
        $this->assertGreaterThanOrEqual(1, substr_count($content, route('resume.download', 'en')));
    }

    public function test_resume_download_route_returns_uploaded_file(): void
    {
        Storage::fake('public');
        $this->seed(PortfolioSeeder::class);
        SiteSetting::query()->updateOrCreate(['key' => 'resume_en_path'], ['value' => 'portfolio/resumes/resume-en.pdf', 'type' => 'string']);
        Storage::disk('public')->put('portfolio/resumes/resume-en.pdf', 'english resume');

        $this
            ->get('/resume/en')
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=tewarach-resume-en.pdf');
    }

    public function test_portfolio_seeder_does_not_clear_uploaded_resume_paths(): void
    {
        SiteSetting::query()->create([
            'key' => 'resume_en_path',
            'value' => 'portfolio/resumes/custom-en.pdf',
            'type' => 'string',
        ]);

        $this->seed(PortfolioSeeder::class);

        $this->assertSame('portfolio/resumes/custom-en.pdf', SiteSetting::value('resume_en_path'));
    }

    public function test_maintenance_mode_hides_public_portfolio_but_keeps_admin_available(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(PortfolioSeeder::class);
        SiteSetting::query()->updateOrCreate(['key' => 'maintenance_mode'], ['value' => '1', 'type' => 'boolean']);

        $this->get('/')
            ->assertStatus(503)
            ->assertSee(__('messages.maintenance_title'));

        $this->get('/login')->assertOk();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/portfolio')
            ->assertOk();
    }

    public function test_public_about_section_can_show_profile_image(): void
    {
        $this->seed(PortfolioSeeder::class);

        ContentBlock::query()
            ->where('key', 'about')
            ->update(['image_path' => 'portfolio/profile.jpg']);

        $this->get('/')
            ->assertOk()
            ->assertSee('portfolio/profile.jpg')
            ->assertSee('Profile photo of');
    }

    public function test_public_contact_uses_real_configured_email_and_hides_empty_links(): void
    {
        $this->seed(PortfolioSeeder::class);

        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact_email'],
            ['value' => 'korw@example.dev', 'type' => 'string']
        );
        SiteSetting::query()->updateOrCreate(
            ['key' => 'github_url'],
            ['value' => 'https://github.com/korwlik', 'type' => 'string']
        );
        SiteSetting::query()->updateOrCreate(
            ['key' => 'phone_number'],
            ['value' => '081-234-5678', 'type' => 'string']
        );

        Link::query()->create([
            'type' => 'discord',
            'label' => 'Discord',
            'url' => '',
            'is_active' => true,
            'display_order' => 99,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('korw@example.dev')
            ->assertSee('mailto:korw@example.dev')
            ->assertSee('data-email-link', false)
            ->assertSee('contact-email-status')
            ->assertSee(__('messages.email_copied'))
            ->assertSee('https://github.com/korwlik')
            ->assertSee(__('messages.urgent_contact'))
            ->assertSee('081-234-5678')
            ->assertSee('tel:0812345678')
            ->assertDontSee('hello@example.com')
            ->assertDontSee('href="https://github.com/"', false)
            ->assertDontSee('Discord');
    }

    public function test_public_project_images_have_accessible_alt_text(): void
    {
        $this->seed(PortfolioSeeder::class);

        $project = Project::query()->where('slug', 'laravel-admin-starter')->firstOrFail();
        $project->update(['image_path' => 'portfolio/projects/admin-starter.jpg']);

        $this->get('/')
            ->assertOk()
            ->assertSee('portfolio/projects/admin-starter.jpg')
            ->assertSee('Screenshot of Laravel Admin Starter');
    }

    public function test_portfolio_project_import_command_creates_projects_from_json(): void
    {
        $path = storage_path('framework/testing/portfolio-projects.json');
        $this->writeImportFile($path, [
            'projects' => [
                [
                    'name' => 'AI Imported Project',
                    'slug' => 'ai-imported-project',
                    'type' => 'tool',
                    'status' => 'draft',
                    'description_th' => 'โปรเจกต์ที่ import จาก JSON',
                    'description_en' => 'Project imported from JSON',
                    'is_featured' => true,
                    'is_public' => true,
                    'display_order' => 20,
                    'tech_stack' => ['Laravel', 'OpenAI', 'MySQL'],
                    'features' => [
                        ['th' => 'สร้างจากไฟล์ JSON', 'en' => 'Created from a JSON file'],
                        ['th' => 'รองรับ batch import', 'en' => 'Supports batch import'],
                    ],
                ],
            ],
        ]);

        $exitCode = Artisan::call('portfolio:import-projects', ['path' => $path]);

        $this->assertSame(0, $exitCode);
        $project = Project::query()->where('slug', 'ai-imported-project')->firstOrFail();
        $this->assertTrue($project->is_featured);
        $this->assertTrue($project->is_public);
        $this->assertSame('tool', $project->type);
        $this->assertSame(20, $project->display_order);
        $this->assertSame(['Laravel', 'MySQL', 'OpenAI'], $project->techStacks()->pluck('name')->all());
        $this->assertCount(2, $project->features);
    }

    public function test_portfolio_project_import_command_can_dry_run_without_writing(): void
    {
        $path = storage_path('framework/testing/portfolio-projects-dry-run.json');
        $this->writeImportFile($path, [
            'projects' => [
                [
                    'name' => 'Dry Run Project',
                    'slug' => 'dry-run-project',
                    'description_en' => 'This should not be written',
                ],
            ],
        ]);

        $exitCode = Artisan::call('portfolio:import-projects', [
            'path' => $path,
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseMissing('projects', ['slug' => 'dry-run-project']);
        $this->assertStringContainsString('Dry-run passed', Artisan::output());
    }

    public function test_portfolio_project_import_command_updates_existing_project_relations(): void
    {
        $project = Project::query()->create([
            'name' => 'Existing Project',
            'slug' => 'existing-project',
            'status' => 'draft',
            'is_public' => false,
            'display_order' => 1,
        ]);
        $project->techStacks()->create(['name' => 'Old Stack']);
        $project->features()->create(['description_en' => 'Old feature', 'display_order' => 1]);

        $path = storage_path('framework/testing/portfolio-projects-update.json');
        $this->writeImportFile($path, [
            'projects' => [
                [
                    'name' => 'Existing Project Updated',
                    'slug' => 'existing-project',
                    'status' => 'live',
                    'is_public' => true,
                    'tech_stack' => ['Laravel', 'Tailwind CSS'],
                    'features' => [
                        ['en' => 'Updated feature'],
                    ],
                ],
            ],
        ]);

        $exitCode = Artisan::call('portfolio:import-projects', ['path' => $path]);

        $this->assertSame(0, $exitCode);
        $project->refresh();
        $this->assertSame('Existing Project Updated', $project->name);
        $this->assertSame('live', $project->status);
        $this->assertTrue($project->is_public);
        $this->assertSame(['Laravel', 'Tailwind CSS'], $project->techStacks()->pluck('name')->all());
        $this->assertSame(['Updated feature'], $project->features()->pluck('description_en')->all());
    }

    public function test_portfolio_project_import_command_rejects_invalid_json_shape(): void
    {
        $path = storage_path('framework/testing/portfolio-projects-invalid.json');
        $this->writeImportFile($path, [
            'projects' => [
                [
                    'name' => 'Invalid Project',
                    'slug' => 'invalid-project',
                    'type' => 'unknown',
                ],
            ],
        ]);

        $exitCode = Artisan::call('portfolio:import-projects', ['path' => $path]);

        $this->assertSame(1, $exitCode);
        $this->assertDatabaseMissing('projects', ['slug' => 'invalid-project']);
        $this->assertStringContainsString('Import validation failed', Artisan::output());
    }

    private function writeImportFile(string $path, array $payload): void
    {
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
