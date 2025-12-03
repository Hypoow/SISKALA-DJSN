<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = Activity::orderBy('date_time', 'desc');

        // Filter by Type
        if ($request->has('type') && in_array($request->type, ['external', 'internal'])) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $activities = $query->get();

        // Group by Month-Year
        $groupedActivities = $activities->groupBy(function($item) {
            return $item->date_time->format('F Y');
        });

        return view('activities.index', compact('groupedActivities'));
    }

    public function create()
    {
        return view('activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_type' => 'required|in:external,internal',
            'name' => 'required|string|max:255',
            'date_time' => 'required|date',
            'status' => 'required|integer',
            'invitation_status' => 'required|integer',
            'invitation_type' => 'required|in:inbound,outbound',
            'location_type' => 'required|in:offline,online',
            'location' => 'nullable|required_if:location_type,offline|string',
            'meeting_link' => 'nullable|required_if:location_type,online|url',
            'pic' => 'nullable|array',
            'dispo_note' => 'nullable|string',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Map activity_type to type
        $validated['type'] = $validated['activity_type'];
        unset($validated['activity_type']);

        if ($request->hasFile('attachment_path')) {
            $path = $request->file('attachment_path')->store('attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        Activity::create($validated);

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function show(Activity $activity)
    {
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        return view('activities.create', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_time' => 'required|date',
            'status' => 'required|integer',
            'invitation_status' => 'required|integer',
            'invitation_type' => 'required|in:inbound,outbound',
            'location_type' => 'required|in:offline,online',
            'location' => 'nullable|required_if:location_type,offline|string',
            'meeting_link' => 'nullable|required_if:location_type,online|url',
            'pic' => 'nullable|array',
            'dispo_note' => 'nullable|string',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('attachment_path')) {
            $path = $request->file('attachment_path')->store('attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        // Clear location/link based on type
        if ($validated['location_type'] === 'offline') {
            $validated['meeting_link'] = null;
        } else {
            $validated['location'] = null;
        }

        $activity->update($validated);

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil dihapus');
    }
}
