<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::firstOrCreate(['key' => 'maintenance_mode'], ['value' => '0']);

        if (User::where('role', 'admin')->doesntExist()) {
            User::factory()->create(['role' => 'admin', 'is_active' => true]);
        }
    }

    public function test_site_is_accessible_when_maintenance_is_off(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Site en maintenance');
    }

    public function test_visitor_sees_maintenance_page_when_maintenance_is_on(): void
    {
        Setting::where('key', 'maintenance_mode')->update(['value' => '1']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Site en maintenance');
    }

    public function test_admin_can_access_site_during_maintenance(): void
    {
        Setting::where('key', 'maintenance_mode')->update(['value' => '1']);

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Site en maintenance');
    }

    public function test_non_admin_user_is_blocked_during_maintenance(): void
    {
        Setting::where('key', 'maintenance_mode')->update(['value' => '1']);

        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Site en maintenance');
    }

    public function test_admin_can_toggle_maintenance(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->post('/admin/maintenance');

        $this->assertTrue(Setting::isMaintenance());

        $this->actingAs($admin)->post('/admin/maintenance');

        $this->assertFalse(Setting::isMaintenance());
    }

    public function test_all_routes_are_blocked_during_maintenance_including_auth(): void
    {
        Setting::where('key', 'maintenance_mode')->update(['value' => '1']);

        $response = $this->get('/auth/connexion');

        $response->assertStatus(200);
        $response->assertSee('Site en maintenance');
    }

    public function test_maintenance_login_endpoint_is_accessible_during_maintenance(): void
    {
        Setting::where('key', 'maintenance_mode')->update(['value' => '1']);

        $response = $this->post('/maintenance/login', [
            'email' => User::where('role', 'admin')->first()->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }
}
