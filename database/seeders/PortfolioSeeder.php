<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Models\Experience;
use App\Models\Link;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Starter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->contentBlocks();
        $this->projects();
        $this->starters();
        $this->skills();
        $this->services();
        $this->experiences();
        $this->links();
        $this->seo();
    }

    private function settings(): void
    {
        $settings = [
            'site_name' => 'Tewarach Portfolio CMS',
            'logo_path' => null,
            'favicon_path' => null,
            'default_language' => 'en',
            'contact_email' => null,
            'phone_number' => '',
            'github_url' => 'https://github.com/',
            'linkedin_url' => 'https://www.linkedin.com/',
            'facebook_url' => 'https://www.facebook.com/',
            'discord_url' => '',
            'line_url' => '',
            'resume_url' => '',
            'resume_th_path' => null,
            'resume_en_path' => null,
            'maintenance_mode' => '0',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::query()->firstOrCreate(['key' => $key], [
                'value' => $value,
                'type' => is_bool($value) ? 'boolean' : 'string',
            ]);
        }
    }

    private function contentBlocks(): void
    {
        ContentBlock::query()->updateOrCreate(['key' => 'hero'], [
            'title_en' => 'Fullstack Developer for Practical Web Systems',
            'title_th' => 'Fullstack Developer สำหรับระบบเว็บที่ใช้งานได้จริง',
            'subtitle_en' => 'I build Laravel, Next.js, and MySQL-based web applications, admin dashboards, SaaS starters, and cloud-deployed demo systems for real business use cases.',
            'subtitle_th' => 'ผมพัฒนาเว็บแอปด้วย Laravel, Next.js และ MySQL ตั้งแต่ระบบหลังบ้าน dashboard, SaaS starter ไปจนถึงการ deploy ระบบขึ้น cloud เพื่อใช้งานจริง',
            'primary_cta_text_en' => 'View Live Projects',
            'primary_cta_text_th' => 'ดูโปรเจกต์ที่ใช้งานจริง',
            'primary_cta_url' => '#projects',
            'secondary_cta_text_en' => 'Contact Me',
            'secondary_cta_text_th' => 'ติดต่อผม',
            'secondary_cta_url' => '#contact',
            'settings' => [
                'availability' => 'Available for selected freelance work and full-time opportunities',
                'stats' => [
                    ['label_en' => 'Core Stack', 'label_th' => 'สแต็กหลัก', 'value' => 'Laravel / Next.js'],
                    ['label_en' => 'Cloud Focus', 'label_th' => 'Cloud Focus', 'value' => 'Azure VM'],
                    ['label_en' => 'Database', 'label_th' => 'ฐานข้อมูล', 'value' => 'MySQL / MariaDB'],
                ],
            ],
            'is_active' => true,
            'display_order' => 1,
        ]);

        ContentBlock::query()->updateOrCreate(['key' => 'about'], [
            'title_en' => 'About Tewarach',
            'title_th' => 'เกี่ยวกับ Tewarach',
            'body_en' => 'I focus on practical web systems: clean admin dashboards, business workflows, SaaS starters, and deployment-ready demos. My work connects Laravel backend foundations with modern frontend experiences and cloud deployment practices.',
            'body_th' => 'ผมโฟกัสการพัฒนาระบบเว็บที่ใช้งานได้จริง ตั้งแต่ dashboard หลังบ้าน workflow สำหรับธุรกิจ SaaS starter และ demo ที่พร้อม deploy โดยเชื่อม Laravel backend กับ frontend สมัยใหม่และแนวทาง cloud deployment',
            'settings' => [
                'years_experience' => '3+',
                'highlights_en' => [
                    'Builds secure Laravel admin systems with role-based access',
                    'Creates SaaS and business workflow starter projects',
                    'Deploys Laravel and Next.js demos on Azure VM infrastructure',
                ],
                'highlights_th' => [
                    'พัฒนาระบบหลังบ้าน Laravel พร้อม role-based access',
                    'สร้าง SaaS และ business workflow starter',
                    'Deploy Laravel และ Next.js demo บน Azure VM',
                ],
                'quick_facts_en' => [
                    ['label' => 'Location', 'value' => 'Thailand'],
                    ['label' => 'Role', 'value' => 'Fullstack Developer'],
                    ['label' => 'Core Stack', 'value' => 'Laravel / Next.js / MySQL'],
                    ['label' => 'Focus', 'value' => 'Admin Systems / SaaS / Deployment'],
                    ['label' => 'Available', 'value' => 'Full-time & Freelance'],
                ],
                'quick_facts_th' => [
                    ['label' => 'ที่อยู่', 'value' => 'ประเทศไทย'],
                    ['label' => 'บทบาท', 'value' => 'Fullstack Developer'],
                    ['label' => 'Core Stack', 'value' => 'Laravel / Next.js / MySQL'],
                    ['label' => 'โฟกัส', 'value' => 'Admin Systems / SaaS / Deployment'],
                    ['label' => 'รับงาน', 'value' => 'Full-time & Freelance'],
                ],
            ],
            'is_active' => true,
            'display_order' => 2,
        ]);

        ContentBlock::query()->updateOrCreate(['key' => 'deployment'], [
            'title_en' => 'Cloud Deployment Case Study',
            'title_th' => 'Cloud Deployment Case Study',
            'subtitle_en' => 'Multiple Laravel and Next.js demo projects can run on one Azure VM through Nginx reverse proxy, PHP-FPM, PM2, MySQL/MariaDB, SSL, and Cloudflare DNS.',
            'subtitle_th' => 'สามารถ deploy หลายโปรเจกต์ Laravel และ Next.js บน Azure VM เครื่องเดียว โดยใช้ Nginx reverse proxy, PHP-FPM, PM2, MySQL/MariaDB, SSL และ Cloudflare DNS',
            'body_en' => 'The architecture uses Cloudflare for DNS, Nginx as the entry point, PHP-FPM for Laravel apps, PM2 for Node/Next.js apps, and one MySQL/MariaDB server with separate databases per project.',
            'body_th' => 'โครงสร้างใช้ Cloudflare สำหรับ DNS, Nginx เป็น entry point, PHP-FPM สำหรับ Laravel, PM2 สำหรับ Node/Next.js และ MySQL/MariaDB server เดียวพร้อมแยก database ตามโปรเจกต์',
            'settings' => [
                'steps' => ['Cloudflare DNS', 'Azure VM', 'Nginx Reverse Proxy', 'Laravel Apps / Next.js Apps', 'PHP-FPM / PM2', 'MySQL / MariaDB Databases', 'SSL / Certbot'],
            ],
            'is_active' => true,
            'display_order' => 9,
        ]);
    }

    private function projects(): void
    {
        $projects = [
            ['bdo-commu', 'bdo-commu', 'Community platform starter for organized game/community content.', 'ระบบ community platform สำหรับจัดการข้อมูลและคอนเทนต์ของชุมชน', ['Laravel', 'Blade', 'MySQL']],
            ['Laravel Admin Starter', 'laravel-admin-starter', 'Secure Laravel admin foundation with auth, roles, settings, translations, and audit logs.', 'Laravel starter สำหรับระบบ admin พร้อม auth, roles, settings, translations และ audit logs', ['Laravel', 'Spatie Permission', 'Tailwind CSS']],
            ['Laravel + Next.js SaaS Starter', 'laravel-nextjs-saas-starter', 'Fullstack SaaS starter concept with Laravel API and Next.js frontend.', 'แนวคิด SaaS starter แบบ fullstack ใช้ Laravel API และ Next.js frontend', ['Laravel', 'Next.js', 'Sanctum']],
            ['Accounting SaaS / SpendSwift', 'accounting-saas-spendswift', 'Business workflow system for income, expenses, reports, and admin operations.', 'ระบบ workflow ธุรกิจสำหรับรายรับ รายจ่าย รายงาน และการจัดการหลังบ้าน', ['Laravel', 'MySQL', 'Dashboard']],
            ['Restaurant Platform Starter', 'restaurant-platform-starter', 'Starter architecture for menu, ordering, branch, and admin workflows.', 'Starter สำหรับระบบร้านอาหาร เมนู ออเดอร์ สาขา และ dashboard', ['Laravel', 'Tailwind CSS', 'MySQL']],
            ['School SaaS Starter', 'school-saas-starter', 'Starter concept for school management workflows, users, roles, and reporting.', 'แนวคิด starter สำหรับระบบโรงเรียน ผู้ใช้ roles และรายงาน', ['Laravel', 'RBAC', 'Reports']],
        ];

        foreach ($projects as $index => [$name, $slug, $descriptionEn, $descriptionTh, $tech]) {
            $project = Project::query()->updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'type' => str_contains(strtolower($name), 'starter') ? 'starter' : 'case-study',
                'status' => $index < 2 ? 'live' : 'demo',
                'description_en' => $descriptionEn,
                'description_th' => $descriptionTh,
                'case_study_en' => 'Built as a practical showcase of Laravel architecture, admin workflows, database-backed content, and deployment-minded project structure.',
                'case_study_th' => 'สร้างเพื่อโชว์การออกแบบ Laravel architecture, admin workflow, content ที่จัดการผ่าน database และโครงสร้างที่พร้อม deploy',
                'is_featured' => $index < 3,
                'is_public' => true,
                'display_order' => $index + 1,
            ]);

            $project->techStacks()->delete();
            foreach ($tech as $item) {
                $project->techStacks()->create(['name' => $item]);
            }

            $project->features()->delete();
            foreach (['Admin workflow', 'Responsive UI', 'Deployment-ready structure'] as $featureIndex => $feature) {
                $project->features()->create([
                    'description_en' => $feature,
                    'description_th' => ['ระบบหลังบ้าน', 'Responsive UI', 'โครงสร้างพร้อม deploy'][$featureIndex],
                    'display_order' => $featureIndex + 1,
                ]);
            }
        }
    }

    private function starters(): void
    {
        $starters = [
            'Laravel Admin Starter' => ['Laravel', 'Blade', 'Tailwind CSS'],
            'Next.js SaaS Starter' => ['Next.js', 'TypeScript', 'Tailwind CSS'],
            'Laravel + Next.js Fullstack Starter' => ['Laravel', 'Next.js', 'Sanctum'],
            'Accounting SaaS Starter' => ['Laravel', 'MySQL', 'Reports'],
            'Restaurant Platform Starter' => ['Laravel', 'Ordering', 'Dashboard'],
            'School SaaS Starter' => ['Laravel', 'Roles', 'Reports'],
            'Cloud Deployment Starter' => ['Azure VM', 'Nginx', 'PM2', 'Certbot'],
        ];

        $order = 1;
        foreach ($starters as $name => $stack) {
            Starter::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'description_en' => "{$name} for faster project delivery with practical defaults.",
                'description_th' => "{$name} สำหรับเริ่มโปรเจกต์ได้เร็วขึ้นด้วยโครงสร้างที่ใช้งานได้จริง",
                'stack' => $stack,
                'status' => 'available',
                'setup_notes_en' => 'Designed for local Laravel/Vite development and production deployment refinement.',
                'setup_notes_th' => 'ออกแบบสำหรับพัฒนา local ด้วย Laravel/Vite และต่อยอดสู่ production deployment',
                'deploy_notes_en' => 'Can be deployed behind Nginx with PHP-FPM or PM2 depending on the stack.',
                'deploy_notes_th' => 'สามารถ deploy ผ่าน Nginx ร่วมกับ PHP-FPM หรือ PM2 ตาม stack ของโปรเจกต์',
                'is_public' => true,
                'display_order' => $order++,
            ]);
        }
    }

    private function skills(): void
    {
        $skills = [
            'Backend' => ['Laravel', 'PHP', 'REST API', 'Authentication', 'Role / Permission', 'Payment Workflow'],
            'Frontend' => ['Next.js', 'Svelte / Vite', 'Tailwind CSS', 'Responsive UI', 'Theme System', 'Multi-language UI'],
            'Database' => ['MySQL', 'MariaDB', 'PostgreSQL / Supabase', 'Migration', 'Seeder', 'Database Design'],
            'DevOps' => ['Azure VM', 'Cloudflare', 'VPS', 'Nginx', 'PHP-FPM', 'PM2', 'SSL / Certbot', 'Git / GitHub'],
            'Product' => ['SaaS Architecture', 'Admin Dashboard', 'Business Workflow', 'System Specification', 'Project Roadmap'],
            'Tools' => ['VS Code', 'Composer', 'npm', 'Vite', 'Postman', 'phpMyAdmin', 'GitHub Actions'],
            'AI' => ['ChatGPT', 'OpenAI Codex', 'GitHub Copilot', 'Cursor', 'Claude', 'Gemini'],
        ];

        $order = 1;
        foreach ($skills as $category => $items) {
            foreach ($items as $item) {
                Skill::query()->updateOrCreate(['name' => $item], [
                    'category' => $category,
                    'level' => in_array($item, ['Laravel', 'PHP', 'MySQL', 'Tailwind CSS', 'Admin Dashboard'], true) ? 'advanced' : 'intermediate',
                    'display_order' => $order++,
                    'is_active' => true,
                ]);
            }
        }
    }

    private function services(): void
    {
        $services = [
            ['Web Application Development', 'พัฒนาเว็บแอปพลิเคชัน', 'Custom business web apps, admin dashboards, and operational systems.', 'พัฒนาเว็บแอป ระบบหลังบ้าน และระบบ workflow สำหรับธุรกิจ'],
            ['Laravel Development', 'พัฒนา Laravel', 'Laravel backend, auth, permissions, CRUD systems, APIs, and dashboards.', 'พัฒนา Laravel backend, auth, permissions, CRUD, API และ dashboard'],
            ['Frontend Development', 'พัฒนา Frontend', 'Responsive Blade, Tailwind, Next.js, and Svelte/Vite interfaces.', 'พัฒนา UI responsive ด้วย Blade, Tailwind, Next.js และ Svelte/Vite'],
            ['Deployment & Server Setup', 'ติดตั้งและ Deploy ระบบ', 'Azure VM, Nginx, PHP-FPM, PM2, SSL, and Cloudflare DNS setup.', 'ตั้งค่า Azure VM, Nginx, PHP-FPM, PM2, SSL และ Cloudflare DNS'],
            ['Bug Fixing & System Improvement', 'แก้บั๊กและปรับปรุงระบบ', 'Debugging, refactoring, performance fixes, and security improvements.', 'แก้บั๊ก refactor ปรับ performance และเพิ่มความปลอดภัย'],
            ['SaaS / MVP Starter Development', 'พัฒนา SaaS / MVP Starter', 'Starter systems for validating products quickly with clean admin foundations.', 'พัฒนา starter สำหรับทดลองสินค้าอย่างรวดเร็วพร้อมฐาน admin ที่สะอาด'],
        ];

        foreach ($services as $index => [$titleEn, $titleTh, $descriptionEn, $descriptionTh]) {
            Service::query()->updateOrCreate(['title_en' => $titleEn], [
                'title_th' => $titleTh,
                'description_en' => $descriptionEn,
                'description_th' => $descriptionTh,
                'price_range' => null,
                'is_active' => true,
                'display_order' => $index + 1,
            ]);
        }
    }

    private function experiences(): void
    {
        $items = [
            ['Laravel Admin & CMS Systems', 'ระบบ Laravel Admin และ CMS', 'Built reusable admin foundations with authentication, roles, settings, translations, and audit trails.', 'สร้างฐานระบบ admin ที่นำกลับมาใช้ได้ พร้อม authentication, roles, settings, translations และ audit trails', ['Laravel', 'Spatie Permission', 'Tailwind CSS']],
            ['SaaS Starter Planning', 'วางแผน SaaS Starter', 'Designed practical starter systems for accounting, restaurant, school, and fullstack SaaS workflows.', 'ออกแบบ starter สำหรับ accounting, restaurant, school และ fullstack SaaS workflow', ['Laravel', 'MySQL', 'Product Roadmap']],
            ['Cloud Demo Deployment', 'Deploy Demo บน Cloud', 'Planned multi-project deployment with subdomains, reverse proxy, SSL, PHP-FPM, PM2, and shared database infrastructure.', 'วางแผน deploy หลายโปรเจกต์ด้วย subdomain, reverse proxy, SSL, PHP-FPM, PM2 และ database infrastructure ร่วมกัน', ['Azure VM', 'Nginx', 'Cloudflare']],
        ];

        foreach ($items as $index => [$titleEn, $titleTh, $descriptionEn, $descriptionTh, $stack]) {
            Experience::query()->updateOrCreate(['title_en' => $titleEn], [
                'title_th' => $titleTh,
                'company' => null,
                'period' => 'Portfolio highlight',
                'description_en' => $descriptionEn,
                'description_th' => $descriptionTh,
                'tech_stack' => $stack,
                'display_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }

    private function links(): void
    {
        $links = [
            ['github', 'GitHub', 'https://github.com/'],
            ['linkedin', 'LinkedIn', 'https://www.linkedin.com/'],
            ['facebook', 'Facebook', 'https://www.facebook.com/'],
            ['website', 'Website', url('/')],
        ];

        foreach ($links as $index => [$type, $label, $url]) {
            Link::query()->updateOrCreate(['type' => $type], [
                'label' => $label,
                'url' => $url,
                'is_active' => true,
                'display_order' => $index + 1,
            ]);
        }
    }

    private function seo(): void
    {
        SeoSetting::query()->updateOrCreate(['page_key' => 'home'], [
            'meta_title_en' => 'Tewarach - Fullstack Developer for Practical Web Systems',
            'meta_title_th' => 'Tewarach - Fullstack Developer สำหรับระบบเว็บที่ใช้งานได้จริง',
            'meta_description_en' => 'Portfolio of Tewarach, a fullstack developer building Laravel, Next.js, MySQL, admin dashboards, SaaS starters, and cloud-deployed demo systems.',
            'meta_description_th' => 'Portfolio ของ Tewarach นักพัฒนา fullstack ที่สร้าง Laravel, Next.js, MySQL, admin dashboard, SaaS starter และระบบ demo ที่ deploy ขึ้น cloud',
            'keywords' => 'Tewarach, Laravel developer, Fullstack developer, Next.js, MySQL, Azure VM, Cloudflare, Nginx',
            'canonical_url' => url('/'),
        ]);
    }
}
