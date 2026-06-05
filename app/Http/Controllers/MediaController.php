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

        // Run self-healing routine for missing thumbnails
        $this->healMediaThumbnails($media);

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
            'type'      => 'required|in:video,photo',
            'url'       => 'nullable|required_if:type,video|url',
            'file'      => 'nullable|required_if:type,photo|file|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'title'     => 'nullable|string|max:255',
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

            $thumbnailPath = null;

            // 1. Check if user uploaded a custom thumbnail (override)
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $path = $file->store('thumbnails', 'public');
                $thumbnailPath = 'storage/' . $path;
            }
            // 2. Otherwise, attempt to fetch thumbnail automatically based on platform
            else {
                if ($mediaType === 'tiktok') {
                    try {
                        $response = $this->secureGet('https://www.tiktok.com/oembed?url=' . urlencode($url));
                        if ($response->successful()) {
                            $data = $response->json();
                            if (!empty($data['thumbnail_url'])) {
                                $thumbnailPath = $this->downloadThumbnail($data['thumbnail_url'], 'tiktok_' . md5($url)) ?? $data['thumbnail_url'];
                            }
                        }
                    } catch (\Exception $e) {
                        // Fail silently
                    }
                } elseif ($mediaType === 'facebook' || $mediaType === 'instagram') {
                    try {
                        // Use a bot User-Agent so Meta platforms serve Open Graph meta tags instead of login redirects
                        $response = $this->secureGet($url, [
                            'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_patched.html)'
                        ]);

                        if ($response->successful()) {
                            $html = $response->body();
                            $ogImageUrl = null;
                            if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $matches)) {
                                $ogImageUrl = html_entity_decode($matches[1]);
                            } elseif (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/i', $html, $matches)) {
                                $ogImageUrl = html_entity_decode($matches[1]);
                            }

                            if ($ogImageUrl) {
                                $thumbnailPath = $this->downloadThumbnail($ogImageUrl, $mediaType . '_' . md5($url)) ?? $ogImageUrl;
                            }
                        }
                    } catch (\Exception $e) {
                        // Fail silently
                    }
                }
            }

            $profile->media()->create([
                'type'  => $mediaType,
                'url'   => $url,
                'path'  => $thumbnailPath,
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
     * Resolve a domain to an IP using Google DNS over HTTPS.
     */
    private function resolveViaDoH(string $domain): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://dns.google/resolve', [
                'name' => $domain,
                'type' => 'A'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['Answer'])) {
                    foreach ($data['Answer'] as $answer) {
                        if ($answer['type'] === 1 && !empty($answer['data'])) {
                            return $answer['data'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }

        return null;
    }

    /**
     * Perform a GET request bypassing broken DNS resolution by querying DoH.
     */
    private function secureGet(string $url, array $headers = [], int $timeout = 10)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $options = [];

        if ($host) {
            $ip = $this->resolveViaDoH($host);
            if ($ip) {
                $options['curl'] = [
                    CURLOPT_RESOLVE => [
                        "{$host}:443:{$ip}",
                        "{$host}:80:{$ip}",
                    ]
                ];
            }
        }

        return \Illuminate\Support\Facades\Http::withHeaders($headers)
            ->withOptions($options)
            ->timeout($timeout)
            ->get($url);
    }

    /**
     * Download an external image and save it to the public storage.
     */
    private function downloadThumbnail(string $url, string $filenameBase): ?string
    {
        try {
            $response = $this->secureGet($url, [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ]);

            if ($response->successful()) {
                $content = $response->body();
                if ($content) {
                    $extension = 'jpg';
                    $contentType = $response->header('Content-Type');
                    if (str_contains($contentType, 'image/png')) {
                        $extension = 'png';
                    } elseif (str_contains($contentType, 'image/webp')) {
                        $extension = 'webp';
                    } elseif (str_contains($contentType, 'image/gif')) {
                        $extension = 'gif';
                    }

                    $filename = $filenameBase . '.' . $extension;
                    Storage::disk('public')->put('thumbnails/' . $filename, $content);
                    return 'storage/thumbnails/' . $filename;
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }

        return null;
    }

    /**
     * Self-healing routine to fetch missing thumbnails for existing media items.
     */
    private function healMediaThumbnails($media)
    {
        foreach ($media as $item) {
            if (in_array($item->type, ['tiktok', 'facebook', 'instagram']) && empty($item->path)) {
                $thumbnailPath = null;
                $url = $item->url;

                if ($item->type === 'tiktok') {
                    try {
                        $response = $this->secureGet('https://www.tiktok.com/oembed?url=' . urlencode($url));
                        if ($response->successful()) {
                            $data = $response->json();
                            if (!empty($data['thumbnail_url'])) {
                                $thumbnailPath = $this->downloadThumbnail($data['thumbnail_url'], 'tiktok_' . md5($url)) ?? $data['thumbnail_url'];
                            }
                        }
                    } catch (\Exception $e) {
                        // Fail silently
                    }
                } elseif ($item->type === 'facebook' || $item->type === 'instagram') {
                    try {
                        $response = $this->secureGet($url, [
                            'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_patched.html)'
                        ]);

                        if ($response->successful()) {
                            $html = $response->body();
                            $ogImageUrl = null;
                            if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $matches)) {
                                $ogImageUrl = html_entity_decode($matches[1]);
                            } elseif (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/i', $html, $matches)) {
                                $ogImageUrl = html_entity_decode($matches[1]);
                            }

                            if ($ogImageUrl) {
                                $thumbnailPath = $this->downloadThumbnail($ogImageUrl, $item->type . '_' . md5($url)) ?? $ogImageUrl;
                            }
                        }
                    } catch (\Exception $e) {
                        // Fail silently
                    }
                }

                if ($thumbnailPath) {
                    $item->path = $thumbnailPath;
                    $item->save();
                }
            }
        }
    }

    /**
     * Remove the specified media asset from database and disk.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $profile = $request->user()->profile;
        $mediaItem = $profile->media()->findOrFail($id);

        // Delete any local file or custom/fetched thumbnail associated with the media
        if ($mediaItem->path && !str_starts_with($mediaItem->path, 'http')) {
            $relativeStoragePath = str_replace('storage/', '', $mediaItem->path);
            if ($relativeStoragePath && Storage::disk('public')->exists($relativeStoragePath)) {
                Storage::disk('public')->delete($relativeStoragePath);
            }
        }

        $mediaItem->delete();

        return redirect()->back()->with('success', 'Medio eliminado correctamente.');
    }
}
