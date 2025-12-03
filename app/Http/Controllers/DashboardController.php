<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function getEvents(Request $request)
    {
        $events = [];
        $activities = Activity::all();

        foreach ($activities as $activity) {
            $color = $activity->type == 'external' ? '#fd7e14' : '#007bff'; // Orange vs Blue
            
            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => $activity->date_time->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('activities.show', $activity->id),
                'extendedProps' => [
                    'type' => $activity->type,
                    'location' => $activity->location_type == 'online' ? 'Online' : $activity->location,
                    'status' => $activity->status
                ]
            ];
        }

        return response()->json($events);
    }
    
    // Placeholder for other methods if needed by routes
    public function store(Request $request) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
