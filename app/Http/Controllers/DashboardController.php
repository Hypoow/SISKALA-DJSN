<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', $this->buildSummary());
    }

    public function getEvents(Request $request)
    {
        $events = [];
        $activities = Activity::query()
                                ->visibleToUser(auth()->user())
                                ->get();

        foreach ($activities as $activity) {
            $color = $activity->type == 'external' ? '#17a2b8' : '#004085'; // Light Blue (External) vs Dark Blue (Internal)
            $displayPicGroups = $activity->shouldNotifyDewanLeadsForUndisposed()
                ? ['Ketua DJSN', 'Ketua Komisi']
                : $activity->display_pic_groups;
            
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
                    'pic' => $displayPicGroups,
                    'pic_details' => collect($displayPicGroups)->mapWithKeys(function ($group) use ($activity) {
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

    public function summary(): JsonResponse
    {
        return response()->json($this->buildSummary());
    }
    
    // Placeholder for other methods if needed by routes
    public function store(Request $request) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}

    private function buildSummary(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $user = auth()->user();
        $query = Activity::visibleToUser($user)
            ->whereMonth('start_date', $currentMonth)
            ->whereYear('start_date', $currentYear);

        return [
            'totalActivities' => (clone $query)->count(),
            'internalActivities' => (clone $query)->where('type', 'internal')->count(),
            'externalActivities' => (clone $query)->where('type', 'external')->count(),
            'todayActivities' => Activity::visibleToUser($user)
                ->whereDate('start_date', now()->today())
                ->count(),
        ];
    }
}
