<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        if (app()->environment('production')) {
            abort(404);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja la respuesta del inicio de sesión de Google.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        if (app()->environment('production')) {
            abort(404);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Error al autenticar con Google. Inténtalo de nuevo.',
            ]);
        }

        // 1. Buscar usuario por google_id o por email para vincularlo
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Actualizar google_id o avatar si no los tenía
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            // Crear nuevo usuario si no existe
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(24)), // Contraseña aleatoria segura
            ]);
        }

        // 2. Crear perfil (TPV) si no lo tiene
        if (!$user->profile) {
            $user->profile()->create([
                'name' => $user->name,
                'slug' => Str::slug($user->name) . '-' . uniqid(),
                'widget_status' => [
                    'agenda' => true,
                    'media' => true,
                    'instagram' => '',
                    'spotify' => '',
                    'whatsapp' => '',
                    'facebook' => '',
                    'youtube' => ''
                ],
            ]);
        }

        // 3. Iniciar sesión y redireccionar a la raíz
        Auth::login($user, true);

        return redirect('/');
    }
}
