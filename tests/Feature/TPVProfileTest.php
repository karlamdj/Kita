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
}
