<?php

namespace Tests\Feature;

use App\Models\Experience;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExperienceTimelineTest extends TestCase
{
    public function test_period_and_duration_count_both_end_months(): void
    {
        $job = new Experience(['kind' => 'work', 'started_on' => '2020-06-01', 'ended_on' => '2020-10-01']);

        $this->assertSame(5, $job->durationInMonths());
        $this->assertSame('Jun 2020 – Oct 2020', $job->periodLabel('en'));
        $this->assertSame('5 mos', $job->durationLabel('en'));
        $this->assertSame('มิ.ย. 2020 – ต.ค. 2020', $job->periodLabel('th'));
        $this->assertSame('5 เดือน', $job->durationLabel('th'));
    }

    public function test_ongoing_role_runs_to_the_current_month(): void
    {
        $this->travelTo(Carbon::parse('2026-10-23'));

        $job = new Experience(['kind' => 'work', 'started_on' => '2025-10-01']);

        $this->assertTrue($job->isOngoing());
        $this->assertSame('Oct 2025 – Present', $job->periodLabel('en'));
        $this->assertSame('1 yr 1 mo', $job->durationLabel('en'));
    }

    public function test_education_shows_years_only_and_falls_back_to_period_text(): void
    {
        $degree = new Experience(['kind' => 'education', 'started_on' => '2017-01-01', 'ended_on' => '2022-01-01']);
        $legacy = new Experience(['kind' => 'highlight', 'period' => 'Portfolio highlight']);

        $this->assertSame('2017 – 2022', $degree->periodLabel('en'));
        $this->assertSame('Portfolio highlight', $legacy->periodLabel('en'));
        $this->assertNull($legacy->durationInMonths());
    }

    public function test_bullets_split_lines_and_strip_markers(): void
    {
        $job = new Experience(['description_en' => "• First point\n\n- Second point\r\n  Third point  "]);

        $this->assertSame(['First point', 'Second point', 'Third point'], $job->bullets('en'));
    }

    public function test_career_summary_merges_overlapping_jobs_and_skips_other_kinds(): void
    {
        $summary = Experience::careerSummary(collect([
            new Experience(['kind' => 'work', 'started_on' => '2020-01-01', 'ended_on' => '2020-12-01']),
            new Experience(['kind' => 'work', 'started_on' => '2020-06-01', 'ended_on' => '2021-06-01']),
            new Experience(['kind' => 'work', 'started_on' => '2023-01-01', 'ended_on' => '2023-06-01']),
            new Experience(['kind' => 'education', 'started_on' => '2016-01-01', 'ended_on' => '2020-01-01']),
            new Experience(['kind' => 'highlight']),
        ]));

        $this->assertSame(24, $summary['months']);
        $this->assertSame(2.0, $summary['years']);
        $this->assertSame(2020, $summary['since']);
        $this->assertSame(3, $summary['roles']);
    }
}
