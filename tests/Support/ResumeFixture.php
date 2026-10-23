<?php

namespace Tests\Support;

trait ResumeFixture
{
    /** Fixed resume data so tests do not depend on the editable database/seeders/data/resume.php. */
    protected function resumeFixture(array $overrides = []): array
    {
        return array_replace([
            'profile' => [
                'owner_name' => 'Test Person',
                'owner_title_en' => 'Full-Stack Web Developer',
                'owner_title_th' => 'นักพัฒนาเว็บ Full-Stack',
                'location_en' => 'Bangkok, Thailand',
                'location_th' => 'กรุงเทพฯ ประเทศไทย',
                'location_code' => 'BKK',
                'timezone' => 'Asia/Bangkok',
                'hero_highlight_en' => 'practical',
                'hero_highlight_th' => '',
            ],
            'experiences' => [
                [
                    'key' => 'acme',
                    'kind' => 'work',
                    'title_en' => 'Junior Programmer',
                    'title_th' => 'โปรแกรมเมอร์',
                    'company' => 'Acme Co',
                    'from' => '2023-06',
                    'to' => '2026-06',
                    'description_en' => ['Built the back office.', 'Wrote the exam API.'],
                    'description_th' => ['สร้างระบบหลังบ้าน', 'เขียน API ระบบสอบ'],
                    'tech_stack' => ['Laravel'],
                ],
                [
                    'key' => 'intern',
                    'kind' => 'work',
                    'title_en' => 'Programmer (Internship)',
                    'company' => 'Hyphen Co',
                    'from' => '2020-06',
                    'to' => '2020-10',
                    'description_en' => ['Helped maintain PHP apps.'],
                ],
                [
                    'key' => 'uni',
                    'kind' => 'education',
                    'title_en' => 'B.Sc. Computer Science',
                    'company' => 'Test University',
                    'from' => '2017',
                    'to' => '2022',
                    'description_en' => ['Faculty of Science'],
                ],
                [
                    'key' => 'highlight-cloud',
                    'kind' => 'highlight',
                    'title_en' => 'Cloud Demo Deployment',
                    'description_en' => ['Planned multi-project deployment.'],
                ],
            ],
            'core_stack' => ['Laravel'],
            'skills' => [
                'Backend' => ['Laravel', 'PHP'],
                'Integration' => ['n8n'],
                'Tools' => ['Postman'],
                'AI' => ['OpenAI Codex'],
            ],
            'services' => [
                ['title_en' => 'Workflow Automation with n8n', 'title_th' => 'ระบบอัตโนมัติด้วย n8n', 'description_en' => 'Automate repetitive work.'],
            ],
            'deployment' => [
                'nodes' => [
                    ['layer' => 'edge', 'name' => 'Cloudflare DNS', 'note' => 'proxied'],
                    ['layer' => 'host', 'name' => 'Azure VM', 'note' => ''],
                    ['layer' => 'data', 'name' => 'MySQL / MariaDB Databases', 'note' => 'persistent'],
                ],
                'steps' => ['Cloudflare DNS', 'SSL / Certbot'],
            ],
        ], $overrides);
    }
}
