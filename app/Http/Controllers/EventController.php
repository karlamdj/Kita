<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    /**
     * Display the musician's calendar inside the dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->profile;

        // Auto-create base profile if it doesn't exist yet
        if (!$profile) {
            $profile = $user->profile()->create([
                'name' => $user->name,
                'slug' => \Illuminate\Support\Str::slug($user->name) . '-' . uniqid(),
                'widget_status' => ['agenda' => true, 'media' => true],
            ]);
        }

        $events = $profile->events()
            ->orderBy('start_time', 'asc')
            ->get();

        return Inertia::render('Dashboard/Calendar', [
            'events' => $events,
        ]);
    }

    /**
     * Store a newly created event or availability block.
     */
    public function store(Request $request): RedirectResponse
    {
        $profile = $request->user()->profile;

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:concierto,ensayo,disponible',
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Merge date and times into Carbon datetime objects
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('start_time'));
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('end_time'));

        $profile->events()->create([
            'title' => $request->input('title'),
            'type' => $request->input('type'),
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'location' => $request->input('location') ?? 'Sin ubicación',
            'description' => $request->input('description'),
            'status' => 'confirmed',
        ]);

        return redirect()->back()->with('success', 'Fecha / Disponibilidad guardada correctamente.');
    }

    /**
     * Remove the specified event/block.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $profile = $request->user()->profile;
        $event = $profile->events()->findOrFail($id);

        $event->delete();

        return redirect()->back()->with('success', 'Fecha eliminada correctamente.');
    }
}
