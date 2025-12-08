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
        // Start from beginning of today (Upcoming)
        $query = Activity::where('date_time', '>=', now()->startOfDay())
                         ->orderBy('date_time', 'asc');

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

        // Group by Month-Year (Indonesian)
        $groupedActivities = $activities->groupBy(function($item) {
            return $item->date_time->isoFormat('MMMM Y');
        });

        return view('activities.index', compact('groupedActivities'));
    }

    public function past(Request $request)
    {
        // Past Activities (Before Today)
        $query = Activity::where('date_time', '<', now()->startOfDay())
                         ->orderBy('date_time', 'desc'); // Newest past first

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

        // Group by Month-Year (Indonesian)
        $groupedActivities = $activities->groupBy(function($item) {
            return $item->date_time->isoFormat('MMMM Y');
        });

        return view('activities.past', compact('groupedActivities'));
    }

    public function uploadMinutes(Request $request, Activity $activity)
    {
        $request->validate([
            'minutes_path' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('minutes_path')) {
            // Delete old file if exists
            if ($activity->minutes_path) {
                \Storage::disk('public')->delete($activity->minutes_path);
            }

            $path = $request->file('minutes_path')->store('minutes', 'public');
            $activity->update(['minutes_path' => $path]);
        }

        return redirect()->back()->with('success', 'Notulensi berhasil diupload.');
    }

    public function uploadAssignmentLetter(Request $request, Activity $activity)
    {
        $request->validate([
            'assignment_letter_path' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('assignment_letter_path')) {
            // Delete old file if exists
            if ($activity->assignment_letter_path) {
                \Storage::disk('public')->delete($activity->assignment_letter_path);
            }

            $path = $request->file('assignment_letter_path')->store('assignment_letters', 'public');
            $activity->update(['assignment_letter_path' => $path]);
        }

        return redirect()->back()->with('success', 'Surat Tugas berhasil diupload.');
    }

    public function create(Request $request)
    {
        $date = $request->query('date');
        return view('activities.create', compact('date'));
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
            'meeting_link' => 'nullable|required_if:location_type,online|string',
            'pic' => 'nullable|array',
            'pic_external' => 'nullable|string',
            'dispo_note' => 'nullable|string',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'minutes_path' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Map activity_type to type
        $validated['type'] = $validated['activity_type'];
        unset($validated['activity_type']);

        // Handle PIC
        if ($validated['type'] == 'external' && $request->filled('pic_external')) {
            $validated['pic'] = [$request->pic_external];
        }
        unset($validated['pic_external']);

        if ($request->hasFile('attachment_path')) {
            $path = $request->file('attachment_path')->store('attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        if ($request->hasFile('minutes_path')) {
            $path = $request->file('minutes_path')->store('minutes', 'public');
            $validated['minutes_path'] = $path;
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
            'meeting_link' => 'nullable|required_if:location_type,online|string',
            'pic' => 'nullable|array',
            'pic_external' => 'nullable|string',
            'dispo_note' => 'nullable|string',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'minutes_path' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('attachment_path')) {
            $path = $request->file('attachment_path')->store('attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        if ($request->hasFile('minutes_path')) {
            $path = $request->file('minutes_path')->store('minutes', 'public');
            $validated['minutes_path'] = $path;
        }

        // Handle PIC
        // Note: activity_type might not be in validated if it's disabled, check request or model
        $type = $request->input('activity_type', $activity->type);
        
        if ($type == 'external' && $request->filled('pic_external')) {
            $validated['pic'] = [$request->pic_external];
        }
        unset($validated['pic_external']);

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
