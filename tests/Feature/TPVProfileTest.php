<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TPVProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_tpv_profile_can_be_updated_with_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        
        // Create initial profile
        $profile = $user->profiles()->create([
            'name' => 'Original Name',
            'slug' => 'original-slug',
            'widget_status' => ['agenda' => true, 'media' => true],
            'theme' => 'kita-neon',
        ]);

        $photo = UploadedFile::fake()->image('profile.jpg');

        $response = $this
            ->actingAs($user)
            ->put('/dashboard/tpv/editar', [
                'name' => 'Updated Name',
                'slug' => 'updated-slug',
                'bio' => 'Updated bio',
                'profile_photo' => $photo,
                'theme' => 'cyber-purple',
                'coverage_area' => ['Sayulita'],
                'instruments' => ['Guitarrista'],
                'widget_status' => ['agenda' => true, 'media' => true],
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('dashboard.tpv.edit'));

        $profile->refresh();

        $this->assertEquals('Updated Name', $profile->name);
        $this->assertEquals('updated-slug', $profile->slug);
        $this->assertEquals('Updated bio', $profile->bio);
        $this->assertEquals('cyber-purple', $profile->theme);
        $this->assertNotNull($profile->profile_photo_path);
        
        // Assert file was stored
        $storedPath = str_replace('storage/', '', $profile->profile_photo_path);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_tpv_profile_can_be_deleted_with_correct_password(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        
        $profile = $user->profiles()->create([
            'name' => 'Delete Me',
            'slug' => 'delete-me',
            'profile_photo_path' => 'storage/profiles/avatar.jpg',
            'widget_status' => ['agenda' => true, 'media' => true],
            'theme' => 'kita-neon',
        ]);

        // Place a fake file to check if it's deleted
        Storage::disk('public')->put('profiles/avatar.jpg', 'fake content');

        $response = $this
            ->actingAs($user)
            ->delete("/dashboard/tpv/{$profile->id}", [
                'password' => 'password123',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('profiles', [
            'id' => $profile->id,
        ]);

        Storage::disk('public')->assertMissing('profiles/avatar.jpg');
    }

    public function test_tpv_profile_cannot_be_deleted_with_incorrect_password(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        
        $profile = $user->profiles()->create([
            'name' => 'Keep Me',
            'slug' => 'keep-me',
            'profile_photo_path' => 'storage/profiles/avatar.jpg',
            'widget_status' => ['agenda' => true, 'media' => true],
            'theme' => 'kita-neon',
        ]);

        Storage::disk('public')->put('profiles/avatar.jpg', 'fake content');

        $response = $this
            ->actingAs($user)
            ->delete("/dashboard/tpv/{$profile->id}", [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors(['password']);
        
        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
        ]);

        Storage::disk('public')->assertExists('profiles/avatar.jpg');
    }

    public function test_tpv_profile_can_be_deleted_by_google_user_confirming_name(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'google_id' => 'google-oauth-id-123',
        ]);
        
        $profile = $user->profiles()->create([
            'name' => 'My Artist Profile',
            'slug' => 'my-artist-profile',
            'profile_photo_path' => 'storage/profiles/avatar.jpg',
            'widget_status' => ['agenda' => true, 'media' => true],
            'theme' => 'kita-neon',
        ]);

        Storage::disk('public')->put('profiles/avatar.jpg', 'fake content');

        $response = $this
            ->actingAs($user)
            ->delete("/dashboard/tpv/{$profile->id}", [
                'profile_name_confirmation' => 'My Artist Profile',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('profiles', [
            'id' => $profile->id,
        ]);

        Storage::disk('public')->assertMissing('profiles/avatar.jpg');
    }

    public function test_tpv_profile_cannot_be_deleted_by_google_user_with_incorrect_name_confirmation(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'google_id' => 'google-oauth-id-123',
        ]);
        
        $profile = $user->profiles()->create([
            'name' => 'My Artist Profile',
            'slug' => 'my-artist-profile',
            'profile_photo_path' => 'storage/profiles/avatar.jpg',
            'widget_status' => ['agenda' => true, 'media' => true],
            'theme' => 'kita-neon',
        ]);

        Storage::disk('public')->put('profiles/avatar.jpg', 'fake content');

        $response = $this
            ->actingAs($user)
            ->delete("/dashboard/tpv/{$profile->id}", [
                'profile_name_confirmation' => 'Wrong Profile Name',
            ]);

        $response->assertSessionHasErrors(['profile_name_confirmation']);
        
        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
        ]);

        Storage::disk('public')->assertExists('profiles/avatar.jpg');
    }
}

