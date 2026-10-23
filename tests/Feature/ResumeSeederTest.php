<?php

namespace Tests\Feature;

use App\Models\ContentBlock;
use App\Models\Experience;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use Database\Seeders\PortfolioSeeder;
use Database\Seeders\ResumeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\Support\ResumeFixture;
use Tests\TestCase;

class ResumeSeederTest extends TestCase
{
    use RefreshDatabase, ResumeFixture;

    public function test_it_writes_profile_experiences_skills_services_and_deployment(): void
    {
        ContentBlock::query()->create(['key' => 'deployment', 'settings' => ['other' => 'kept']]);

        (new ResumeSeeder)->run($this->resumeFixture());

        $this->assertSame('Test Person', SiteSetting::value('owner_name'));
        $this->assertSame('Asia/Bangkok', SiteSetting::value('timezone'));

        $job = Experience::query()->where('key', 'acme')->firstOrFail();
        $this->assertSame('work', $job->kind);
        $this->assertSame('2023-06-01', $job->started_on->toDateString());
        $this->assertSame('2026-06-01', $job->ended_on->toDateString());
        $this->assertSame("Built the back office.\nWrote the exam API.", $job->description_en);
        $this->assertSame(['Laravel'], $job->tech_stack);
        $this->assertSame('Programmer (Internship)', Experience::query()->where('key', 'intern')->value('title_th'));
        $this->assertSame('education', Experience::query()->where('key', 'uni')->value('kind'));

        $this->assertSame('advanced', Skill::query()->where('name', 'Laravel')->value('level'));
        $this->assertSame('intermediate', Skill::query()->where('name', 'PHP')->value('level'));
        $this->assertSame('Integration', Skill::query()->where('name', 'n8n')->value('category'));

        $this->assertTrue(Service::query()->where('title_en', 'Workflow Automation with n8n')->value('is_active'));

        $settings = ContentBlock::query()->where('key', 'deployment')->value('settings');
        $this->assertSame('kept', $settings['other']);
        $this->assertCount(3, $settings['nodes']);
        $this->assertSame(['layer' => 'edge', 'name' => 'Cloudflare DNS', 'note' => 'proxied'], $settings['nodes'][0]);
        $this->assertSame(['Cloudflare DNS', 'SSL / Certbot'], $settings['steps']);
    }

    public function test_rerunning_updates_in_place_and_hides_removed_entries(): void
    {
        $fixture = $this->resumeFixture();
        (new ResumeSeeder)->run($fixture);

        $fixture['experiences'][0]['title_en'] = 'Programmer';
        unset($fixture['experiences'][1]);
        $fixture['skills']['Backend'] = ['Laravel'];
        $fixture['services'] = [];
        (new ResumeSeeder)->run($fixture);

        $this->assertSame(1, Experience::query()->where('key', 'acme')->count());
        $this->assertSame('Programmer', Experience::query()->where('key', 'acme')->value('title_en'));
        $this->assertFalse(Experience::query()->where('key', 'intern')->value('is_active'));
        $this->assertFalse(Skill::query()->where('name', 'PHP')->value('is_active'));
        $this->assertFalse(Service::query()->where('title_en', 'Workflow Automation with n8n')->value('is_active'));
    }

    public function test_it_adopts_rows_created_before_keys_existed(): void
    {
        Experience::query()->create([
            'kind' => 'highlight',
            'title_en' => 'Cloud Demo Deployment',
            'title_th' => 'Deploy Demo',
            'period' => 'Portfolio highlight',
            'is_active' => true,
        ]);

        (new ResumeSeeder)->run($this->resumeFixture());

        $this->assertSame(1, Experience::query()->where('title_en', 'Cloud Demo Deployment')->count());
        $this->assertSame('highlight-cloud', Experience::query()->where('title_en', 'Cloud Demo Deployment')->value('key'));
    }

    public function test_invalid_data_is_rejected_and_nothing_is_written(): void
    {
        $cases = [
            "unknown category 'Mobile'" => ['skills' => ['Mobile' => ['Flutter']]],
            "use 'YYYY-MM' or 'YYYY'" => ['experiences' => [['key' => 'x', 'title_en' => 'X', 'from' => 'June 2023']]],
            "kind 'job' must be one of" => ['experiences' => [['key' => 'x', 'kind' => 'job', 'title_en' => 'X']]],
            'not listed under skills: Docker' => ['core_stack' => ['Docker']],
            "unknown key(s) ownr_name" => ['profile' => ['ownr_name' => 'Typo']],
        ];

        foreach ($cases as $message => $override) {
            try {
                (new ResumeSeeder)->run($this->resumeFixture($override));
                $this->fail("Expected the seeder to reject: {$message}");
            } catch (InvalidArgumentException $exception) {
                $this->assertStringContainsString($message, $exception->getMessage());
            }

            $this->assertDatabaseMissing('site_settings', ['key' => 'owner_name']);
            $this->assertSame(0, Experience::query()->count());
        }
    }

    public function test_bundled_resume_data_file_seeds_and_renders(): void
    {
        $this->seed(PortfolioSeeder::class);
        $this->seed(ResumeSeeder::class);

        $this->assertGreaterThan(0, Experience::query()->where('kind', 'work')->count());

        $this->get('/')->assertOk()->assertSee('id="experience"', false);
    }
}
