<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->timestamps();
        });

        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title_th')->nullable();
            $table->string('title_en')->nullable();
            $table->text('subtitle_th')->nullable();
            $table->text('subtitle_en')->nullable();
            $table->longText('body_th')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('primary_cta_text_th')->nullable();
            $table->string('primary_cta_text_en')->nullable();
            $table->string('primary_cta_url')->nullable();
            $table->string('secondary_cta_text_th')->nullable();
            $table->string('secondary_cta_text_en')->nullable();
            $table->string('secondary_cta_url')->nullable();
            $table->json('settings')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->nullable()->index();
            $table->string('status')->default('concept')->index();
            $table->text('description_th')->nullable();
            $table->text('description_en')->nullable();
            $table->longText('case_study_th')->nullable();
            $table->longText('case_study_en')->nullable();
            $table->string('live_demo_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_public')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->text('description_th')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('project_tech_stacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['project_id', 'name']);
        });

        Schema::create('starters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description_th')->nullable();
            $table->text('description_en')->nullable();
            $table->json('stack')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('status')->default('available')->index();
            $table->longText('setup_notes_th')->nullable();
            $table->longText('setup_notes_en')->nullable();
            $table->longText('deploy_notes_th')->nullable();
            $table->longText('deploy_notes_en')->nullable();
            $table->boolean('is_public')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->index();
            $table->string('level')->default('intermediate')->index();
            $table->string('icon')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title_th');
            $table->string('title_en');
            $table->text('description_th')->nullable();
            $table->text('description_en')->nullable();
            $table->string('price_range')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('title_th');
            $table->string('title_en');
            $table->string('company')->nullable();
            $table->string('period')->nullable();
            $table->text('description_th')->nullable();
            $table->text('description_en')->nullable();
            $table->json('tech_stack')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('label')->nullable();
            $table->string('url');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('meta_title_th')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->text('meta_description_th')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->string('og_image_path')->nullable();
            $table->text('keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
        Schema::dropIfExists('links');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('services');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('starters');
        Schema::dropIfExists('project_tech_stacks');
        Schema::dropIfExists('project_features');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('site_settings');
    }
};
