<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get Stats for Current Month
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $user = auth()->user();
        $query = Activity::whereMonth('date_time', $currentMonth)->whereYear('date_time', $currentYear);

        // If Dewan, technically they see all, but maybe highlight theirs? 
        // For stats, let's just show ALL activities for now as "Overview", 
        // or filter by what they are involved in?
        // User request was general "dashboard display", so macro stats are best.
        
        $totalActivities = (clone $query)->count();
        $internalActivities = (clone $query)->where('type', 'internal')->count();
        $externalActivities = (clone $query)->where('type', 'external')->count();
        
        // Today's Activities
        $todayActivities = Activity::whereDate('date_time', now()->today())->count();

        return view('dashboard.index', compact('totalActivities', 'internalActivities', 'externalActivities', 'todayActivities'));
    }

    public function getEvents(Request $request)
    {
        $events = [];
        $activities = Activity::all();

        foreach ($activities as $activity) {
            $color = $activity->type == 'external' ? '#fd7e14' : '#007bff'; // Default: Orange vs Blue

            // Dewan Role Logic
            if (auth()->check() && auth()->user()->role === 'Dewan') {
                $dispositionTo = $activity->disposition_to ?? [];
                // Ensure disposition_to is an array
                if (!is_array($dispositionTo)) {
                    $dispositionTo = [];
                }

                if (in_array(auth()->user()->name, $dispositionTo)) {
                    // Must Attend (Targeted)
                    // Differentiate by Type
                    if ($activity->type == 'internal') {
                        $color = '#007bff'; // Blue for Internal
                    } else {
                        $color = '#fd7e14'; // Orange for External
                    }
                } else {
                    $color = '#6c757d'; // Gray for "Info Only" activity (Not targeted)
                }
            }
            
            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => $activity->date_time->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                // 'url' => route('activities.show', $activity->id), // Removed to prevent auto-navigation
                'extendedProps' => [
                    'type' => $activity->type,
                    'location_type' => $activity->location_type,
                    'location_detail' => $activity->location,
                    'meeting_link' => $activity->meeting_link,
                    'pic' => $activity->pic,
                    'description' => $activity->dispo_note ? strip_tags($activity->dispo_note) : '-',
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
