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
     * Store a newly created media asset (video URL or uploaded photo/video).
     */
    public function store(Request $request): RedirectResponse
    {
        $profile = $request->user()->profile;

        // Validation based on type (increased limit to 100MB for video files)
        $request->validate([
            'type' => 'required|in:video,photo',
            'url' => 'nullable|required_if:type,video|url',
            'file' => 'nullable|required_if:type,photo|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,webm|max:102400',
            'title' => 'nullable|string|max:255',
        ]);

        if ($request->input('type') === 'video') {
            $url = $request->input('url');
            $mediaType = 'youtube';

            if (Str::contains($url, ['vimeo.com'])) {
                $mediaType = 'vimeo';
            }

            $profile->media()->create([
                'type' => $mediaType,
                'url' => $url,
                'title' => $request->input('title') ?? 'Video promocional',
            ]);
        } else {
            // Store file in public disk (can be a photo or a local video file)
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $mimeType = $file->getMimeType();
                $extension = strtolower($file->getClientOriginalExtension());

                $isLocalVideo = Str::contains($mimeType, 'video/') || in_array($extension, ['mp4', 'mov', 'webm']);
                $mediaType = $isLocalVideo ? 'video' : 'photo';
                $defaultTitle = $isLocalVideo ? 'Video de promoción' : 'Foto de promoción';

                $path = $file->store('media', 'public');

                $profile->media()->create([
                    'type' => $mediaType,
                    'path' => 'storage/' . $path, // Mapped to /storage/media/filename in public
                    'title' => $request->input('title') ?? $defaultTitle,
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
