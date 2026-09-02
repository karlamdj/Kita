<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of events for the active profile.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['events' => []]);
        }

        $events = $profile->events()
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'events' => $events,
        ]);
    }

    /**
     * Store a newly created event or availability block.
     */
    public function store(Request $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado.'], 404);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:concierto,ensayo,disponible',
            'date'        => 'required|date_format:Y-m-d',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('start_time'));
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('end_time'));

        $event = $profile->events()->create([
            'title'       => $request->input('title'),
            'type'        => $request->input('type'),
            'start_time'  => $startDateTime,
            'end_time'    => $endDateTime,
            'location'    => $request->input('location') ?? 'Sin ubicación',
            'description' => $request->input('description'),
            'status'      => 'confirmed',
        ]);

        return response()->json([
            'message' => 'Evento guardado correctamente.',
            'event'   => $event,
        ], 201);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado.'], 404);
        }

        $event = $profile->events()->findOrFail($id);
        $event->delete();

        return response()->json([
            'message' => 'Evento eliminado correctamente.',
        ]);
    }
}
