<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display a listing of media elements for the active profile.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['media' => []]);
        }

        $media = $profile->media()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->healMediaThumbnails($media);

        return response()->json([
            'media' => $media->fresh(),
        ]);
    }

    /**
     * Store a newly created media asset (social video URL or uploaded photo).
     */
    public function store(Request $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado.'], 404);
        }

        $request->validate([
            'type'      => 'required|in:video,photo,video_link',
            'url'       => 'nullable|required_if:type,video,video_link|url',
            'file'      => 'nullable|required_if:type,photo|file|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'photo'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'title'     => 'nullable|string|max:255',
        ]);

        $type = $request->input('type');

        if ($type === 'video' || $type === 'video_link') {
            $url = $request->input('url');

            // Detect platform from URL
            $mediaType = 'youtube';
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

            // 1. Check if user uploaded a custom thumbnail
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $path = $file->store('thumbnails', 'public');
                $thumbnailPath = 'storage/' . $path;
            }
            // 2. Fetch thumbnail automatically based on platform
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

            $media = $profile->media()->create([
                'type'  => $mediaType,
                'url'   => $url,
                'path'  => $thumbnailPath,
                'title' => $request->input('title') ?? 'Video promocional',
            ]);
        } else {
            // Photo upload
            $photoFile = $request->file('file') ?? $request->file('photo');
            if (!$photoFile) {
                return response()->json(['message' => 'No se ha adjuntado ningún archivo de foto.'], 422);
            }

            $path = $photoFile->store('media', 'public');

            $media = $profile->media()->create([
                'type'  => 'photo',
                'path'  => 'storage/' . $path,
                'title' => $request->input('title') ?? 'Foto de promoción',
            ]);
        }

        return response()->json([
            'message' => 'Medio añadido correctamente.',
            'media'   => $media,
        ], 201);
    }

    /**
     * Remove the specified media asset.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado.'], 404);
        }

        $mediaItem = $profile->media()->findOrFail($id);

        // Delete physical file if stored locally
        if ($mediaItem->path && !str_starts_with($mediaItem->path, 'http')) {
            $relativeStoragePath = str_replace('storage/', '', $mediaItem->path);
            if ($relativeStoragePath && Storage::disk('public')->exists($relativeStoragePath)) {
                Storage::disk('public')->delete($relativeStoragePath);
            }
        }

        $mediaItem->delete();

        return response()->json([
            'message' => 'Medio eliminado correctamente.',
        ]);
    }

    /**
     * Resolve domain to IP via DNS over HTTPS to avoid local lookup blocks.
     */
    private function resolveViaDoH(string $domain): ?string
    {
        try {
            $response = Http::get('https://dns.google/resolve', [
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
     * Perform a GET request bypassing broken DNS resolution.
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

        return Http::withHeaders($headers)
            ->withOptions($options)
            ->timeout($timeout)
            ->get($url);
    }

    /**
     * Download external image and save to public storage.
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
    private function healMediaThumbnails($media): void
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
}
