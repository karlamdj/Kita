<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    /**
     * Display the public directory of profiles with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Profile::query()->with([
            'media' => function ($q) {
                $q->orderBy('sort_order')->orderBy('created_at', 'desc');
            },
            'events' => function ($q) {
                $q->where('end_time', '>=', now())
                    ->orderBy('start_time', 'asc');
            },
            'user',
        ]);

        // Filter by instrument (stored as JSON array in database)
        if ($request->filled('instrument')) {
            $query->whereJsonContains('instruments', $request->input('instrument'));
        }

        // Filter by zone (coverage area - stored as JSON array or text)
        if ($request->filled('zone')) {
            $zone = $request->input('zone');
            $query->where(function ($q) use ($zone) {
                $q->whereJsonContains('coverage_area', $zone)
                  ->orWhere('coverage_area', 'like', "%{$zone}%");
            });
        }

        // Search in name, bio, and coverage area
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%")
                  ->orWhere('coverage_area', 'like', "%{$search}%");
            });
        }

        $musicians = $query->orderBy('name')->get();

        $defaultInstruments = ['Guitarrista', 'Baterista', 'Bajista', 'Vocalista', 'Banda Completa', 'Tecladista', 'Percusionista'];
        $dbInstruments = Profile::whereNotNull('instruments')
            ->pluck('instruments')
            ->flatten()
            ->unique()
            ->filter()
            ->values()
            ->toArray();
        $instrumentsList = array_values(array_unique(array_merge($defaultInstruments, $dbInstruments)));
        sort($instrumentsList);

        return response()->json([
            'musicians'       => $musicians,
            'filters'         => $request->only(['search', 'instrument', 'zone']),
            'instrumentsList' => $instrumentsList,
        ]);
    }

    /**
     * Display a specific public artist profile TPV by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $profile = Profile::where('slug', $slug)
            ->with([
                'media' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('created_at', 'desc');
                },
                'events' => function ($query) {
                    $query->where('end_time', '>=', now())
                        ->orderBy('start_time', 'asc');
                },
                'user',
            ])
            ->firstOrFail();

        return response()->json([
            'profile' => $profile,
        ]);
    }
}
