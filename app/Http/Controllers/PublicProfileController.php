<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicProfileController extends Controller
{
    /**
     * Display the public directory of profiles with optional filters.
     */
    public function index(Request $request): Response
    {
        $query = Profile::query()->with(['media', 'events']);

        // Filter by instrument (stored as JSON array in database)
        if ($request->filled('instrument')) {
            $query->whereJsonContains('instruments', $request->input('instrument'));
        }

        // Filter by zone (coverage area)
        if ($request->filled('zone')) {
            $query->where('coverage_area', $request->input('zone'));
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

        return Inertia::render('Home', [
            'musicians' => $musicians,
            'filters' => $request->only(['search', 'instrument', 'zone']),
        ]);
    }
}
