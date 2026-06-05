<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_redirect_redirects_to_google(): void
    {
        // Mock del driver de Socialite y del método redirect()
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get('/auth/google');

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_google_callback_creates_user_and_profile_if_not_exists(): void
    {
        // Mock del usuario retornado por Google
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-123456');
        $googleUser->shouldReceive('getName')->andReturn('John Doe');
        $googleUser->shouldReceive('getEmail')->andReturn('johndoe@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar-url');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($googleUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        // Realizar la petición callback
        $response = $this->get('/auth/google/callback');

        // Verificar que el usuario y su perfil hayan sido creados
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'google_id' => 'google-id-123456',
            'avatar' => 'https://lh3.googleusercontent.com/avatar-url',
        ]);

        $user = User::where('email', 'johndoe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->profile);
        $this->assertEquals('John Doe', $user->profile->name);

        // Verificar inicio de sesión y redirección
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }

    public function test_google_callback_links_existing_user_by_email(): void
    {
        // Crear un usuario previo registrado sin Google ID
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => null,
            'avatar' => null,
        ]);

        // Mock del usuario retornado por Google
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-existing');
        $googleUser->shouldReceive('getName')->andReturn($user->name);
        $googleUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar-url');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($googleUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        // Realizar la petición callback
        $response = $this->get('/auth/google/callback');

        // Verificar que el usuario existente se haya actualizado con Google ID y avatar
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'existing@example.com',
            'google_id' => 'google-id-existing',
            'avatar' => 'https://lh3.googleusercontent.com/avatar-url',
        ]);

        // Verificar inicio de sesión
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }

    public function test_google_callback_handles_socialite_exceptions_gracefully(): void
    {
        // Simular que Socialite arroja una excepción
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new \Exception('Invalid OAuth payload'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        // Realizar la petición callback
        $response = $this->get('/auth/google/callback');

        // Verificar redirección de regreso al login con errores
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
