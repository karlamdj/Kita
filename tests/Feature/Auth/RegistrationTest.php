<?php

namespace Tests\Feature\Auth;

use App\Mail\WelcomeUserMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));

        Mail::assertSent(WelcomeUserMail::class, function (WelcomeUserMail $mail) {
            return $mail->hasTo('test@example.com') &&
                   $mail->user->name === 'Test User';
        });
    }

    public function test_registration_with_honeypot_field_is_ignored_and_redirects_to_home(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Spam Bot',
            'email' => 'bot@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'extra_phone' => '555-555-5555',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('home'));
        Mail::assertNothingSent();
    }
}
