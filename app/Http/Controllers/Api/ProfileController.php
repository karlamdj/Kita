<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Get the active artist profile for the authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        // Auto-create base profile if it doesn't exist
        if (!$profile) {
            $profile = $user->profiles()->create([
                'name'          => $user->name,
                'slug'          => Str::slug($user->name) . '-' . uniqid(),
                'bio'           => '',
                'theme'         => 'kita-neon',
                'instruments'   => [],
                'genres'        => [],
                'coverage_area' => [],
                'widget_status' => [
                    'agenda'    => true,
                    'media'     => true,
                    'instagram' => '',
                    'spotify'   => '',
                    'whatsapp'  => '',
                    'facebook'  => '',
                    'youtube'   => '',
                ],
            ]);
        }

        $profile->load([
            'media' => function ($query) {
                $query->orderBy('sort_order')->orderBy('created_at', 'desc');
            },
            'events' => function ($query) {
                $query->where('end_time', '>=', now())
                    ->orderBy('start_time', 'asc');
            },
            'user',
        ]);

        return response()->json([
            'profile' => $profile,
        ]);
    }

    /**
     * Update the active artist profile information.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado.'], 404);
        }

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => 'required|string|max:255|unique:profiles,slug,' . $profile->id,
            'bio'           => 'nullable|string',
            'theme'         => 'nullable|string|in:kita-neon,cyber-purple,volt-orange,electric-red',
            'instruments'   => 'nullable|array',
            'genres'        => 'nullable|array',
            'coverage_area' => 'nullable|array',
            'widget_status' => 'nullable|array',
        ]);

        $profile->update([
            'name'          => $validated['name'],
            'slug'          => Str::slug($validated['slug']),
            'bio'           => $validated['bio'] ?? '',
            'theme'         => $validated['theme'] ?? $profile->theme ?? 'kita-neon',
            'instruments'   => $validated['instruments'] ?? [],
            'genres'        => $validated['genres'] ?? [],
            'coverage_area' => $validated['coverage_area'] ?? [],
            'widget_status' => $validated['widget_status'] ?? $profile->widget_status ?? [],
        ]);

        return response()->json([
            'message' => 'Perfil actualizado exitosamente.',
            'profile' => $profile->fresh(['media', 'events']),
        ]);
    }

    /**
     * Upload and update profile photo.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado.'], 404);
        }

        $request->validate([
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        $file = $request->file('photo') ?? $request->file('profile_photo');

        if (!$file) {
            return response()->json([
                'message' => 'No se ha proporcionado ninguna imagen.',
            ], 422);
        }

        // Delete old photo if it exists
        if ($profile->profile_photo_path) {
            $oldPath = str_replace('storage/', '', $profile->profile_photo_path);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $path = $file->store('profiles', 'public');
        $profilePhotoPath = 'storage/' . $path;

        $profile->update([
            'profile_photo_path' => $profilePhotoPath,
        ]);

        return response()->json([
            'message'            => 'Foto de perfil actualizada correctamente.',
            'profile_photo_path' => $profilePhotoPath,
            'profile'            => $profile->fresh(),
        ]);
    }

    /**
     * Switch the active profile for the authenticated user.
     */
    public function switchProfile(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profiles()->findOrFail($id);

        if ($request->hasSession()) {
            $request->session()->put('active_profile_id', $profile->id);
        }

        return response()->json([
            'message'        => 'Perfil activo cambiado exitosamente.',
            'active_profile' => $profile->load(['media', 'events']),
        ]);
    }

    /**
     * Create an additional artist profile (multi-profile).
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'theme' => 'nullable|string|in:kita-neon,cyber-purple,volt-orange,electric-red',
        ]);

        $profile = $user->profiles()->create([
            'name'          => $validated['name'],
            'slug'          => Str::slug($validated['name']) . '-' . uniqid(),
            'bio'           => '',
            'theme'         => $validated['theme'] ?? 'kita-neon',
            'instruments'   => [],
            'genres'        => [],
            'coverage_area' => [],
            'widget_status' => [
                'agenda'    => true,
                'media'     => true,
                'instagram' => '',
                'spotify'   => '',
                'whatsapp'  => '',
                'facebook'  => '',
                'youtube'   => '',
            ],
        ]);

        return response()->json([
            'message' => 'Perfil artístico creado exitosamente.',
            'profile' => $profile,
        ], 201);
    }

    /**
     * Delete an artist profile and its associated files.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profiles()->findOrFail($id);

        // Delete profile photo
        if ($profile->profile_photo_path) {
            $photoPath = str_replace('storage/', '', $profile->profile_photo_path);
            if (Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
        }

        // Delete associated local media files
        foreach ($profile->media as $mediaItem) {
            if ($mediaItem->path && !str_starts_with($mediaItem->path, 'http')) {
                $filePath = str_replace('storage/', '', $mediaItem->path);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }

        $profile->delete();

        return response()->json([
            'message' => 'Perfil artístico eliminado correctamente.',
        ]);
    }
}
