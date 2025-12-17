<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

use App\Services\GoogleCalendarService;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only([
            'create', 'store', 'edit', 'update', 'destroy',
            'uploadMinutes', 'uploadAssignmentLetter',
            'deleteMinutes', 'deleteAssignment', 'updateSummary'
        ]);
    }

    public function index(Request $request)
    {
        return view('activities.index');
    }

    public function past(Request $request)
    {
        return view('activities.past');
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

    public function deleteMinutes(Activity $activity)
    {
        if ($activity->minutes_path) {
            \Storage::disk('public')->delete($activity->minutes_path);
            $activity->update(['minutes_path' => null]);
            return redirect()->back()->with('success', 'Notulensi berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function deleteAssignment(Activity $activity)
    {
        if ($activity->assignment_letter_path) {
            \Storage::disk('public')->delete($activity->assignment_letter_path);
            $activity->update(['assignment_letter_path' => null]);
            return redirect()->back()->with('success', 'Surat Tugas berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function create(Request $request)
    {
        $date = $request->query('date');
        // Fetch Dewan and DJSN users, then group them
        $users = User::whereIn('role', ['Dewan', 'DJSN'])
                     ->orderBy('order')
                     ->get();
        
        $dewanUsers = $users->groupBy(function($user) {
            if ($user->role === 'DJSN') {
                return 'Sekretariat DJSN';
            }
            return $user->divisi;
        });

        return view('activities.create', compact('date', 'dewanUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_type' => 'required|in:external,internal',
            'letter_number' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'status' => 'required|integer',
            'invitation_status' => 'required|integer',
            'invitation_type' => 'required|in:inbound,outbound',
            'location_type' => 'required|in:offline,online,hybrid',
            'location' => 'nullable|required_if:location_type,offline,hybrid|string',
            'media_online' => 'nullable|string',
            'meeting_link' => 'nullable|string',
            'meeting_id' => 'nullable|string',
            'passcode' => 'nullable|string',
            'pic' => 'nullable|array',
            'pic_external' => 'nullable|string',
            'organizer_name' => 'nullable|string',
            'dispo_note' => 'nullable|string',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'minutes_path' => 'nullable|file|mimes:pdf|max:10240',
            'summary_content' => 'nullable|string',
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
            $file = $request->file('attachment_path');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs('attachments', $filename, 'public');
            $validated['attachment_path'] = $path;
        }

        if ($request->hasFile('minutes_path')) {
            $path = $request->file('minutes_path')->store('minutes', 'public');
            $validated['minutes_path'] = $path;
        }

        // Default End Date to Start Date if null
        if (empty($validated['end_date'])) {
            $validated['end_date'] = $validated['start_date'];
        }

        $activity = Activity::create($validated);

        // Create Google Calendar Event (Asynchronous)
        \App\Jobs\SyncGoogleCalendarEvent::dispatch($activity);
        
        $message = 'Kegiatan berhasil ditambahkan dan sedang diproses untuk integrasi Google Calendar.';

        return redirect()->route('activities.index')->with('success', $message);
    }

    public function show(Activity $activity)
    {
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        // Fetch Dewan and DJSN users, then group them
        $users = User::whereIn('role', ['Dewan', 'DJSN'])
                     ->orderBy('order')
                     ->get();
        
        $dewanUsers = $users->groupBy(function($user) {
            if ($user->role === 'DJSN') {
                return 'Sekretariat DJSN';
            }
            return $user->divisi;
        });

        return view('activities.create', compact('activity', 'dewanUsers'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'letter_number' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'status' => 'required|integer',
            'invitation_status' => 'required|integer',
            'invitation_type' => 'required|in:inbound,outbound',
            'location_type' => 'required|in:offline,online,hybrid',
            'location' => 'nullable|required_if:location_type,offline,hybrid|string',
            'media_online' => 'nullable|string',
            'meeting_link' => 'nullable|string',
            'meeting_id' => 'nullable|string',
            'passcode' => 'nullable|string',
            'pic' => 'nullable|array',
            'pic_external' => 'nullable|string',
            'organizer_name' => 'nullable|string',
            'dispo_note' => 'nullable|string',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'minutes_path' => 'nullable|file|mimes:pdf|max:10240',
            'summary_content' => 'nullable|string',
        ]);

        if ($request->hasFile('attachment_path')) {
            $file = $request->file('attachment_path');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs('attachments', $filename, 'public');
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

        // Clear location/link based on type (Only if switching strictly to offline or online)
        if ($validated['location_type'] === 'offline') {
            $validated['meeting_link'] = null;
        } elseif ($validated['location_type'] === 'online') {
            $validated['location'] = null;
        }
        // If hybrid, keep both.

        // Default End Date to Start Date if null
        if (empty($validated['end_date'])) {
            $validated['end_date'] = $validated['start_date'];
        }

        $activity->update($validated);

        // Update Google Calendar Event
        \App\Jobs\SyncGoogleCalendarEvent::dispatch($activity, true);

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil diperbarui dan sinkronisasi berjalan di latar belakang.');
    }

    public function destroy(Activity $activity)
    {
        // Delete Google Calendar Event
        GoogleCalendarService::deleteEvent($activity);

        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil dihapus');
    }

    public function updateSummary(Request $request, Activity $activity)
    {
        $request->validate([
            'summary_content' => 'required|string',
        ]);

        $activity->update([
            'summary_content' => $request->summary_content
        ]);

        return redirect()->route('activities.show', $activity->id)->with('success', 'Berhasil menginput Hasil Rapat Secara Singkat');
    }
}
