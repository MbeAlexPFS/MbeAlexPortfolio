<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_admin_can_update_profile_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->put('/admin/profil-site', [
            'headline' => 'Développeur Symfony',
            'bio' => 'Passionné depuis 10 ans.',
        ]);

        $response->assertRedirect(route('admin.profile.edit'));

        $this->assertSame('Développeur Symfony', $admin->fresh()->headline);
        $this->assertSame('Passionné depuis 10 ans.', $admin->fresh()->bio);
    }

    public function test_admin_can_upload_an_avatar(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->put('/admin/profil-site', [
            'avatar' => UploadedFile::fake()->image('profile.jpg'),
        ]);

        $response->assertRedirect(route('admin.profile.edit'));

        $user = $admin->fresh();

        $this->assertNotNull($user->avatar_url);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $user->getRawOriginal('avatar_url')));
        $this->assertStringStartsWith('/storage/avatars/', $user->getRawOriginal('avatar_url'));
    }

    public function test_new_avatar_replaces_previous_file_on_disk(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'avatar_url' => 'avatars/ancien-avatar.jpg',
        ]);
        Storage::disk('public')->put('avatars/ancien-avatar.jpg', 'old');

        $this->actingAs($admin)->put('/admin/profil-site', [
            'avatar' => UploadedFile::fake()->image('nouveau.jpg'),
        ]);

        Storage::disk('public')->assertMissing('avatars/ancien-avatar.jpg');

        $this->assertNotSame('avatars/ancien-avatar.jpg', $admin->fresh()->getRawOriginal('avatar_url'));
    }

    public function test_admin_can_remove_current_avatar(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'avatar_url' => 'avatars/a-supprimer.jpg',
        ]);
        Storage::disk('public')->put('avatars/a-supprimer.jpg', 'content');

        $response = $this->actingAs($admin)->put('/admin/profil-site', [
            'remove_avatar' => '1',
        ]);

        $response->assertRedirect(route('admin.profile.edit'));

        $this->assertNull($admin->fresh()->getRawOriginal('avatar_url'));
        Storage::disk('public')->assertMissing('avatars/a-supprimer.jpg');
    }

    public function test_non_image_file_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->put('/admin/profil-site', [
            'avatar' => UploadedFile::fake()->create('document.txt', 100),
        ]);

        $response->assertSessionHasErrors('avatar');

        $this->assertNull($admin->fresh()->getRawOriginal('avatar_url'));
    }
}