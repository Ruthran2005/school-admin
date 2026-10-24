<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_admin_form_starts_with_no_module_access_selected(): void
    {
        $primaryAdmin = User::factory()->create([
            'role' => 'admin',
            'admin_permissions' => null,
        ]);

        $response = $this->actingAs($primaryAdmin)->get('/admin/admins/create');

        $response->assertOk()->assertSee('Module access');
        $this->assertSame(0, substr_count($response->getContent(), 'checked'));
    }

    public function test_admin_can_create_an_account_with_selected_module_access(): void
    {
        $primaryAdmin = User::factory()->create([
            'role' => 'admin',
            'admin_permissions' => null,
        ]);

        $response = $this->actingAs($primaryAdmin)->post('/admin/admins', [
            'name' => 'Fees Admin',
            'email' => 'fees.admin@school.test',
            'phone' => '',
            'password' => 'password123',
            'admin_permissions' => ['invoices', 'fees'],
        ]);

        $response->assertRedirect('/admin/admins');
        $this->assertDatabaseHas('users', [
            'email' => 'fees.admin@school.test',
            'role' => 'admin',
        ]);

        $createdAdmin = User::where('email', 'fees.admin@school.test')->firstOrFail();
        $this->assertSame(['invoices', 'fees'], $createdAdmin->admin_permissions);
    }

    public function test_limited_admin_is_authorized_only_for_granted_modules(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'admin_permissions' => ['students'],
        ]);

        $this->actingAs($admin)->get('/admin/students')->assertOk();
        $this->actingAs($admin)->get('/staff/marks')->assertForbidden();
        $this->actingAs($admin)->get('/admin/admins')->assertForbidden();
    }

    public function test_primary_admin_retains_access_to_every_module(): void
    {
        $primaryAdmin = User::factory()->create([
            'role' => 'admin',
            'admin_permissions' => null,
        ]);

        $this->actingAs($primaryAdmin)->get('/staff/marks')->assertOk();
        $this->actingAs($primaryAdmin)->get('/admin/admins')->assertOk();
    }
}
