<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the public TPV (Tarjeta de Presentación Virtual) for an artist.
     */
    public function show(string $slug): Response
    {
        $profile = Profile::where('slug', $slug)
            ->with(['media' => function ($query) {
                $query->orderBy('sort_order')->orderBy('created_at', 'desc');
            }, 'events' => function ($query) {
                $query->where('end_time', '>=', now())
                    ->orderBy('start_time', 'asc');
            }])
            ->firstOrFail();

        return Inertia::render('Profile/Show', [
            'profile' => $profile,
        ]);
    }

    /**
     * Display the public portfolio preview for the authenticated artist.
     */
    public function showPreview(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->profile;

        // Auto-create base profile if it doesn't exist
        if (!$profile) {
            $profile = $user->profile()->create([
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

        // Load media and events relations
        $profile->load(['media' => function ($query) {
            $query->orderBy('sort_order')->orderBy('created_at', 'desc');
        }, 'events' => function ($query) {
            $query->where('end_time', '>=', now())
                ->orderBy('start_time', 'asc');
        }]);

        return Inertia::render('Profile/Show', [
            'profile' => $profile,
        ]);
    }

    /**
     * Display the private artist TPV configuration/edit form.
     */
    public function editArtist(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->profile;

        // Auto-create base profile if it doesn't exist
        if (!$profile) {
            $profile = $user->profile()->create([
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

        return Inertia::render('Profile/ArtistEdit', [
            'profile' => $profile,
            'is_main_profile' => $profile->id === $user->profiles()->orderBy('id', 'asc')->first()->id,
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the artist's TPV information.
     */
    public function updateArtist(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'coverage_area' => 'nullable|array',
            'instruments' => 'nullable|array',
            'widget_status' => 'nullable|array',
            'slug' => 'required|string|max:255|unique:profiles,slug,' . $profile->id,
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'theme' => 'nullable|string|in:kita-neon,cyber-purple,volt-orange,electric-red',
        ]);

        $data = [
            'name' => $request->input('name'),
            'bio' => $request->input('bio'),
            'coverage_area' => $request->input('coverage_area') ?? [],
            'instruments' => $request->input('instruments') ?? [],
            'widget_status' => $request->input('widget_status') ?? [],
            'slug' => Str::slug($request->input('slug')),
            'theme' => $request->input('theme', 'kita-neon'),
        ];

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if it exists on public disk
            if ($profile->profile_photo_path) {
                $oldPath = str_replace('storage/', '', $profile->profile_photo_path);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }

            // Store new photo in public storage (profiles folder)
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $data['profile_photo_path'] = 'storage/' . $path;
        }

        $profile->update($data);

        return Redirect::route('dashboard.tpv.edit')->with('success', 'Tu TPV se ha actualizado correctamente.');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->google_id) {
            $request->validate([
                'account_email_confirmation' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($user) {
                        if ($value !== $user->email) {
                            $fail('El correo electrónico introducido no coincide con tu correo de cuenta.');
                        }
                    }
                ],
            ]);
        } else {
            $request->validate([
                'password' => ['required', 'current_password'],
            ]);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Delete a specific artist profile.
     */
    public function destroyArtist(Request $request, $id): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profiles()->findOrFail($id);

        if ($user->google_id) {
            $request->validate([
                'profile_name_confirmation' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($profile) {
                        if ($value !== $profile->name) {
                            $fail('El nombre del perfil introducido no coincide con el nombre de tu perfil.');
                        }
                    }
                ],
            ]);
        } else {
            $request->validate([
                'password' => ['required', 'current_password'],
            ]);
        }

        // Delete profile photo from storage if exists
        if ($profile->profile_photo_path) {
            $filePath = str_replace('storage/', '', $profile->profile_photo_path);
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
            }
        }

        // Delete associated local media files
        foreach ($profile->media as $mediaItem) {
            if ($mediaItem->path) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($mediaItem->path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($mediaItem->path);
                }
            }
        }

        // Clean up active profile in session if it is being deleted
        if ($request->session()->get('active_profile_id') == $profile->id) {
            $request->session()->forget('active_profile_id');
        }

        $profile->delete();

        return Redirect::route('dashboard')->with('success', 'El perfil artístico se ha eliminado correctamente.');
    }

    /**
     * Switch the active profile in the session.
     */
    public function switchProfile(Request $request, $id): RedirectResponse
    {
        $user = $request->user();
        
        // Ensure the profile belongs to the authenticated user
        $profile = $user->profiles()->findOrFail($id);
        
        $request->session()->put('active_profile_id', $profile->id);
        
        return redirect()->back();
    }

    /**
     * Show the profile creation form.
     */
    public function createArtist(Request $request): Response
    {
        return Inertia::render('Profile/ArtistCreate');
    }

    /**
     * Store a newly created artist profile.
     */
    public function storeArtist(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'theme' => 'nullable|string|in:kita-neon,cyber-purple,volt-orange,electric-red',
        ]);

        $profile = $user->profiles()->create([
            'name' => $request->input('name'),
            'instruments' => [],
            'coverage_area' => [],
            'bio' => '',
            'theme' => $request->input('theme', 'kita-neon'),
            'slug' => Str::slug($request->input('name')) . '-' . uniqid(),
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

        // Automatically set the newly created profile as active
        $request->session()->put('active_profile_id', $profile->id);

        return Redirect::route('dashboard.tpv.edit')->with('success', '¡Perfil creado con éxito!');
    }
}
