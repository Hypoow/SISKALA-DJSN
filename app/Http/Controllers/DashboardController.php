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
        $query = Activity::visibleToUser($user)
                         ->whereMonth('start_date', $currentMonth)
                         ->whereYear('start_date', $currentYear);

        // If Dewan, technically they see all, but maybe highlight theirs? 
        // For stats, let's just show ALL activities for now as "Overview", 
        // or filter by what they are involved in?
        // User request was general "dashboard display", so macro stats are best.
        
        $totalActivities = (clone $query)->count();
        $internalActivities = (clone $query)->where('type', 'internal')->count();
        $externalActivities = (clone $query)->where('type', 'external')->count();
        
        // Today's Activities
        $todayActivities = Activity::visibleToUser($user)->whereDate('start_date', now()->today())->count();

        return view('dashboard.index', compact('totalActivities', 'internalActivities', 'externalActivities', 'todayActivities'));
    }

    public function getEvents(Request $request)
    {
        $events = [];
        $activities = Activity::query()
                                ->visibleToUser(auth()->user())
                                ->get();

        foreach ($activities as $activity) {
            $color = $activity->type == 'external' ? '#17a2b8' : '#004085'; // Light Blue (External) vs Dark Blue (Internal)
            
            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => \Carbon\Carbon::parse($activity->start_date->format('Y-m-d') . ' ' . $activity->start_time)->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                // 'url' => route('activities.show', $activity->id), // Removed to prevent auto-navigation
                'extendedProps' => [
                    'type' => $activity->type,
                    'location_type' => $activity->location_type,
                    'location' => $activity->location,
                    'media_online' => $activity->media_online,
                    'meeting_link' => $activity->meeting_link,
                    'meeting_id' => $activity->meeting_id,
                    'passcode' => $activity->passcode,
                    'pic' => $activity->display_pic_groups,
                    'pic_details' => collect($activity->display_pic_groups)->mapWithKeys(function ($group) use ($activity) {
                        return [$group => $activity->getDispositionGroupMembers($group)];
                    })->toArray(),
                    'description' => $activity->dispo_note ?? '-',
                    'status' => $activity->status,
                    'organizer_name' => $activity->organizer_name,
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
