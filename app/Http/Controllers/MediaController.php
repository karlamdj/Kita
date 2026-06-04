<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    /**
     * Display a listing of the musician's media inside the private dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->profile;

        // Auto-create a base profile if the user doesn't have one yet
        if (!$profile) {
            $profile = $user->profile()->create([
                'name' => $user->name,
                'slug' => Str::slug($user->name) . '-' . uniqid(),
                'widget_status' => ['agenda' => true, 'media' => true],
            ]);
        }

        $media = $profile->media()->orderBy('sort_order')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Dashboard/Media', [
            'media' => $media,
        ]);
    }

    /**
     * Store a newly created media asset (social video URL or uploaded photo).
     * NOTE: Local video file uploads (.mp4) are prohibited by business rule.
     *       Videos are managed exclusively via external social media links:
     *       YouTube, Facebook, Instagram, TikTok.
     */
    public function store(Request $request): RedirectResponse
    {
        $profile = $request->user()->profile;

        $request->validate([
            'type'  => 'required|in:video,photo',
            'url'   => 'nullable|required_if:type,video|url',
            'file'  => 'nullable|required_if:type,photo|file|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'title' => 'nullable|string|max:255',
        ]);

        if ($request->input('type') === 'video') {
            $url = $request->input('url');

            // Detect platform from URL
            $mediaType = 'youtube'; // default
            if (Str::contains($url, ['facebook.com', 'fb.watch'])) {
                $mediaType = 'facebook';
            } elseif (Str::contains($url, ['instagram.com'])) {
                $mediaType = 'instagram';
            } elseif (Str::contains($url, ['tiktok.com'])) {
                $mediaType = 'tiktok';
            } elseif (Str::contains($url, ['vimeo.com'])) {
                $mediaType = 'vimeo';
            }

            $profile->media()->create([
                'type'  => $mediaType,
                'url'   => $url,
                'title' => $request->input('title') ?? 'Video promocional',
            ]);
        } else {
            // Photo upload only (local video files are not allowed)
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('media', 'public');

                $profile->media()->create([
                    'type'  => 'photo',
                    'path'  => 'storage/' . $path,
                    'title' => $request->input('title') ?? 'Foto de promoción',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Medio añadido correctamente.');
    }

    /**
     * Remove the specified media asset from database and disk.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $profile = $request->user()->profile;
        $mediaItem = $profile->media()->findOrFail($id);

        // Delete local files (both photo and local video files) from storage disk
        if ($mediaItem->type === 'photo' || ($mediaItem->type === 'video' && $mediaItem->path)) {
            // Extract the path from storage url (e.g. storage/media/xxxx.png -> media/xxxx.png)
            $relativeStoragePath = str_replace('storage/', '', $mediaItem->path);
            if ($relativeStoragePath && Storage::disk('public')->exists($relativeStoragePath)) {
                Storage::disk('public')->delete($relativeStoragePath);
            }
        }

        $mediaItem->delete();

        return redirect()->back()->with('success', 'Medio eliminado correctamente.');
    }
}
