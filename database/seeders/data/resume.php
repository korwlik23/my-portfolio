<?php

/*
 * Resume data for the portfolio home page. Edit this file, then run:
 *
 *     php artisan db:seed --class=ResumeSeeder
 *
 * - Every list here is complete: experiences, skills and services that are not
 *   listed get hidden (is_active = false). Nothing is deleted.
 * - Re-running replaces admin edits made to the rows listed here.
 * - Dates: 'YYYY-MM' or 'YYYY'. Leave 'to' as null for a job you still hold.
 * - Skill categories must be one of App\Models\Skill::CATEGORIES.
 */

return [

    'profile' => [
        'owner_name' => 'Tewarach Rakngam',
        'owner_title_en' => 'Full-Stack Web Developer',
        'owner_title_th' => 'นักพัฒนาเว็บ Full-Stack',
        'location_en' => 'Bangkok, Thailand',
        'location_th' => 'กรุงเทพฯ ประเทศไทย',
        'location_code' => 'BKK',
        'timezone' => 'Asia/Bangkok',
        // Word in the hero title that gets the highlight bar. Leave empty for none.
        'hero_highlight_en' => 'practical',
        'hero_highlight_th' => 'จริง',
    ],

    'experiences' => [
        [
            'key' => 'infinite-brain',
            'kind' => 'work',
            'title_en' => 'Junior Programmer',
            'title_th' => 'Junior Programmer',
            'company' => 'Infinite Brain',
            'from' => '2023-06',
            'to' => '2026-06',
            'description_en' => [
                'Developed and maintained back-office systems for employee and student management, including the eiMaths-TH frontend and back-office.',
                'Developed the Spelling Bee backend for exam registration and question management, with RESTful API integration between frontend and backend.',
            ],
            'description_th' => [
                'พัฒนาและดูแลระบบ Back-office สำหรับจัดการพนักงานและนักเรียน รวมถึง Frontend และ Back-office ของ eiMaths-TH',
                'พัฒนา Backend ของระบบ Spelling Bee สำหรับลงทะเบียนสอบและจัดการข้อสอบ พร้อมเชื่อมต่อ RESTful APIs ระหว่าง Frontend และ Backend',
            ],
            'tech_stack' => [],
        ],
        [
            'key' => 'rmutt-arit',
            'kind' => 'work',
            'title_en' => 'IT Specialist / Developer',
            'title_th' => 'IT Specialist / Developer',
            'company' => 'RMUTT-ARIT',
            'from' => '2022-02',
            'to' => '2023-05',
            'description_en' => [
                'Developed an ISO Form system for internal organizational requests.',
                'Developed an overtime management system for employee work tracking.',
            ],
            'description_th' => [
                'พัฒนาระบบ ISO Form สำหรับคำขอภายในองค์กร',
                'พัฒนาระบบจัดการการทำงานล่วงเวลา (OT) สำหรับติดตามการทำงานของพนักงาน',
            ],
            'tech_stack' => [],
        ],
        [
            'key' => 'hyphen-plus-internship',
            'kind' => 'work',
            'title_en' => 'Junior Programmer (Internship)',
            'title_th' => 'Junior Programmer (ฝึกงาน)',
            'company' => 'Hyphen Plus',
            'from' => '2020-06',
            'to' => '2020-10',
            'description_en' => [
                'Assisted in developing and maintaining web applications using PHP and Laravel.',
                'Supported the team in troubleshooting and improving existing systems.',
            ],
            'description_th' => [
                'ช่วยพัฒนาและดูแล Web Application ด้วย PHP และ Laravel',
                'ช่วยแก้ไขปัญหาและปรับปรุงระบบเดิม',
            ],
            'tech_stack' => ['PHP', 'Laravel'],
        ],
        [
            'key' => 'rmutt-bsc-computer-science',
            'kind' => 'education',
            'title_en' => 'B.Sc. Computer Science',
            'title_th' => 'วิทยาศาสตรบัณฑิต สาขาวิทยาการคอมพิวเตอร์',
            'company' => 'Rajamangala University of Technology Thanyaburi',
            'from' => '2017',
            'to' => '2022',
            'description_en' => ['Faculty of Science and Technology'],
            'description_th' => ['คณะวิทยาศาสตร์และเทคโนโลยี'],
            'tech_stack' => [],
        ],
        [
            'key' => 'highlight-admin-cms',
            'kind' => 'highlight',
            'title_en' => 'Laravel Admin & CMS Systems',
            'title_th' => 'ระบบ Laravel Admin และ CMS',
            'description_en' => ['Built reusable admin foundations with authentication, roles, settings, translations, and audit trails.'],
            'description_th' => ['สร้างฐานระบบ admin ที่นำกลับมาใช้ได้ พร้อม authentication, roles, settings, translations และ audit trails'],
            'tech_stack' => ['Laravel', 'Spatie Permission', 'Tailwind CSS'],
        ],
        [
            'key' => 'highlight-saas-starters',
            'kind' => 'highlight',
            'title_en' => 'SaaS Starter Planning',
            'title_th' => 'วางแผน SaaS Starter',
            'description_en' => ['Designed practical starter systems for accounting, restaurant, school, and fullstack SaaS workflows.'],
            'description_th' => ['ออกแบบ starter สำหรับ accounting, restaurant, school และ fullstack SaaS workflow'],
            'tech_stack' => ['Laravel', 'MySQL', 'Product Roadmap'],
        ],
        [
            'key' => 'highlight-cloud-deployment',
            'kind' => 'highlight',
            'title_en' => 'Cloud Demo Deployment',
            'title_th' => 'Deploy Demo บน Cloud',
            'description_en' => ['Planned multi-project deployment with subdomains, reverse proxy, SSL, PHP-FPM, PM2, and shared database infrastructure.'],
            'description_th' => ['วางแผน deploy หลายโปรเจกต์ด้วย subdomain, reverse proxy, SSL, PHP-FPM, PM2 และ database infrastructure ร่วมกัน'],
            'tech_stack' => ['Azure VM', 'Nginx', 'Cloudflare'],
        ],
    ],

    // Shown first in "core stack", the ticker and search-engine data. Each must also appear under 'skills'.
    'core_stack' => ['Laravel', 'PHP', 'TypeScript', 'Next.js', 'MySQL', 'PostgreSQL', 'Docker', 'n8n'],

    'skills' => [
        'Backend' => ['Laravel', 'PHP', 'Node.js', 'REST API', 'Authentication', 'Role / Permission', 'Payment Workflow'],
        'Frontend' => ['TypeScript', 'JavaScript', 'Next.js', 'Svelte / Vite', 'Tailwind CSS', 'HTML', 'Responsive UI', 'Theme System', 'Multi-language UI'],
        'Database' => ['MySQL', 'MariaDB', 'PostgreSQL', 'Supabase', 'SQL', 'Migration', 'Seeder', 'Database Design'],
        'DevOps' => ['Docker', 'Traefik', 'Azure VM', 'Cloudflare', 'VPS', 'Nginx', 'PM2', 'SSL / Certbot', 'CI/CD', 'Git / GitHub'],
        'Integration' => ['n8n', 'Webhooks', 'OAuth', 'LINE Messaging API', 'Meta Graph API'],
        'Product' => ['Admin Dashboard', 'Business Workflow', 'System Specification', 'Project Roadmap'],
        'Tools' => ['VS Code', 'Postman', 'Navicat', 'Xampp', 'Antigravity', 'GitHub/Gitlab'],
        'AI' => ['ChatGPT', 'Claude Code', 'Codex', 'Gemini', 'Perplexity'],
    ],

    'services' => [
        [
            'title_en' => 'Web Application Development',
            'title_th' => 'พัฒนาเว็บแอปพลิเคชัน',
            'description_en' => 'Custom business web apps, admin dashboards, and operational systems.',
            'description_th' => 'พัฒนาเว็บแอป ระบบหลังบ้าน และระบบ workflow สำหรับธุรกิจ',
        ],
        [
            'title_en' => 'Laravel Development',
            'title_th' => 'พัฒนา Laravel',
            'description_en' => 'Laravel backend, auth, permissions, CRUD systems, APIs, and dashboards.',
            'description_th' => 'พัฒนา Laravel backend, auth, permissions, CRUD, API และ dashboard',
        ],
        [
            'title_en' => 'Frontend Development',
            'title_th' => 'พัฒนา Frontend',
            'description_en' => 'Responsive Blade, Tailwind, Next.js, and Svelte/Vite interfaces.',
            'description_th' => 'พัฒนา UI responsive ด้วย Blade, Tailwind, Next.js และ Svelte/Vite',
        ],
        [
            'title_en' => 'Deployment & Server Setup',
            'title_th' => 'ติดตั้งและ Deploy ระบบ',
            'description_en' => 'Azure VM, Nginx, PM2, SSL, and Cloudflare DNS setup.',
            'description_th' => 'ตั้งค่า Azure VM, Nginx, PM2, SSL และ Cloudflare DNS',
        ],
        [
            'title_en' => 'Workflow Automation with n8n',
            'title_th' => 'ระบบอัตโนมัติด้วย n8n',
            'description_en' => 'Automate repetitive work with n8n: webhooks, LINE OA and Meta integrations, and scheduled jobs connected to your existing systems.',
            'description_th' => 'เปลี่ยนงานที่ทำซ้ำให้เป็นอัตโนมัติด้วย n8n เชื่อม webhook, LINE OA, Meta และงานตามเวลา เข้ากับระบบที่มีอยู่',
        ],
        [
            'title_en' => 'Bug Fixing & System Improvement',
            'title_th' => 'แก้บั๊กและปรับปรุงระบบ',
            'description_en' => 'Debugging, refactoring, performance fixes, and security improvements.',
            'description_th' => 'แก้บั๊ก refactor ปรับ performance และเพิ่มความปลอดภัย',
        ],
        [
            'title_en' => 'SaaS / MVP Starter Development',
            'title_th' => 'พัฒนา SaaS / MVP Starter',
            'description_en' => 'Starter systems for validating products quickly with clean admin foundations.',
            'description_th' => 'พัฒนา starter สำหรับทดลองสินค้าอย่างรวดเร็วพร้อมฐาน admin ที่สะอาด',
        ],
    ],

    'deployment' => [
        // Left to right in the diagram. 'layer' and 'note' are the small mono labels.
        'nodes' => [
            ['layer' => 'edge', 'name' => 'Cloudflare DNS', 'note' => 'cached · proxied'],
            ['layer' => 'host', 'name' => 'Azure VM', 'note' => 'ubuntu · running'],
            ['layer' => 'proxy', 'name' => 'Nginx Reverse Proxy', 'note' => 'reverse · ssl'],
            ['layer' => 'apps', 'name' => 'Laravel / Next.js Apps', 'note' => 'php-fpm · pm2'],
            ['layer' => 'data', 'name' => 'MySQL / MariaDB Databases', 'note' => 'persistent'],
        ],
        'steps' => ['Cloudflare DNS', 'Azure VM', 'Nginx Reverse Proxy', 'Laravel Apps / Next.js Apps', 'PHP-FPM / PM2', 'MySQL / MariaDB Databases', 'SSL / Certbot'],
    ],

];
