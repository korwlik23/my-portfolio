<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Translation;
use App\Models\AuditLog;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StarterCoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_can_access_dashboard_with_starter_role(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(__('messages.dashboard'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee(__('messages.system_dashboard'));
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_manage_translation_overrides(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post('/admin/translations', [
                'locale' => 'en',
                'group' => 'messages',
                'key' => 'dashboard',
                'value' => 'Workspace',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('translations', [
            'locale' => 'en',
            'group' => 'messages',
            'key' => 'dashboard',
            'value' => 'Workspace',
        ]);
    }

    public function test_admin_translation_page_lists_language_file_keys(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/translations?locale=en&search=starter_hero_title')
            ->assertOk()
            ->assertSee('starter_hero_title')
            ->assertSee('Laravel starter for production-minded projects.');
    }

    public function test_admin_translation_page_keeps_language_file_order(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/translations?locale=en')
            ->assertOk()
            ->assertSeeInOrder(['dashboard', 'management', 'users']);
    }

    public function test_authenticated_layout_includes_large_text_toggle(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('toggleLargeText', false)
            ->assertSee('Aa');
    }

    public function test_authenticated_layout_links_to_profile(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee(__('messages.profile'));

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(route('profile'), false);
    }

    public function test_admin_can_edit_users(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $user->assignRole('user');

        $this->actingAs($admin)
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()
            ->assertSee('Old Name');

        $this->actingAs($admin)
            ->put("/admin/users/{$user->id}", [
                'name' => 'New Name',
                'email' => 'new@example.com',
                'birth_date' => null,
                'role' => 'user',
                'status' => 'inactive',
            ])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'status' => 'inactive',
        ]);
    }

    public function test_admin_users_page_shows_management_actions(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create(['name' => 'Managed User']);
        $user->assignRole('user');

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Managed User')
            ->assertSee(__('messages.edit'))
            ->assertSee(__('messages.ban_user'))
            ->assertSee(__('messages.reset_password'))
            ->assertSee(__('messages.delete'));
    }

    public function test_admin_can_delete_users_except_self(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($admin)
            ->delete("/admin/users/{$user->id}")
            ->assertRedirect('/admin/users');

        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $this->actingAs($admin)
            ->delete("/admin/users/{$admin->id}")
            ->assertForbidden();
    }

    public function test_admin_can_update_translation_overrides(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $translation = Translation::create([
            'locale' => 'en',
            'group' => 'messages',
            'key' => 'dashboard',
            'value' => 'Dashboard',
        ]);

        $this->actingAs($admin)
            ->put("/admin/translations/{$translation->id}", [
                'value' => 'Workspace',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'value' => 'Workspace',
        ]);
    }

    public function test_database_translation_overrides_language_files(): void
    {
        Translation::create([
            'locale' => 'en',
            'group' => 'messages',
            'key' => 'dashboard',
            'value' => 'Workspace',
        ]);

        Translation::clearCache();
        app()->setLocale('en');

        $this->assertSame('Workspace', __('messages.dashboard'));
    }

    public function test_admin_can_view_audit_logs(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'test.action',
            'description' => 'Audit entry',
        ]);

        $this->actingAs($admin)
            ->get('/admin/audit-logs')
            ->assertOk()
            ->assertSee('Audit entry');
    }
}
