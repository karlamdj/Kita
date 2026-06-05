<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $userProfiles = $user ? $user->profiles()->get() : collect();
        
        $activeProfile = null;
        if ($user) {
            $activeProfileId = $request->session()->get('active_profile_id');
            if ($activeProfileId) {
                $activeProfile = $user->profiles()->find($activeProfileId);
            }
            if (!$activeProfile && $userProfiles->isNotEmpty()) {
                $activeProfile = $userProfiles->first();
                $request->session()->put('active_profile_id', $activeProfile->id);
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'user_profiles' => $userProfiles,
                'active_profile' => $activeProfile,
                'profile_theme' => $activeProfile ? $activeProfile->theme : 'kita-neon',
            ],
        ];
    }
}
