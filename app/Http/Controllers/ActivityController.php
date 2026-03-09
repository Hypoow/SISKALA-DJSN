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
        if (!auth()->user()->canManagePostActivity()) {
             abort(403, 'Anda tidak memiliki hak akses untuk mengupload notulensi.');
        }

        $request->validate([
            'minutes_path' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('minutes_path')) {
            // Delete old file if exists
            if ($activity->minutes_path) {
                \Storage::disk('public')->delete($activity->minutes_path);
            }

            $originalName = $request->file('minutes_path')->getClientOriginalName();
            $path = $request->file('minutes_path')->storeAs("minutes/{$activity->id}", $originalName, 'public');
            $activity->update(['minutes_path' => $path]);
        }

        return redirect()->back()->with('success', 'Notulensi berhasil diupload.');
    }

    public function uploadAssignmentLetter(Request $request, Activity $activity)
    {
        if (!auth()->user()->canUploadAssignment()) {
             abort(403, 'Anda tidak memiliki hak akses untuk mengupload surat tugas.');
        }

        $request->validate([
            'assignment_letter_path' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('assignment_letter_path')) {
            // Delete old file if exists
            if ($activity->assignment_letter_path) {
                \Storage::disk('public')->delete($activity->assignment_letter_path);
            }

            $originalName = $request->file('assignment_letter_path')->getClientOriginalName();
            $path = $request->file('assignment_letter_path')->storeAs("assignment_letters/{$activity->id}", $originalName, 'public');
            $activity->update(['assignment_letter_path' => $path]);
        }

        return redirect()->back()->with('success', 'Surat Tugas berhasil diupload.');
    }

    public function deleteMinutes(Activity $activity)
    {
        if (!auth()->user()->canManagePostActivity()) {
             abort(403, 'Anda tidak memiliki hak akses untuk menghapus notulensi.');
        }

        if ($activity->minutes_path) {
            \Storage::disk('public')->delete($activity->minutes_path);
            $activity->update(['minutes_path' => null]);
            return redirect()->back()->with('success', 'Notulensi berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function deleteAssignment(Activity $activity)
    {
        if (!auth()->user()->canUploadAssignment()) {
             abort(403, 'Anda tidak memiliki hak akses untuk menghapus surat tugas.');
        }

        if ($activity->assignment_letter_path) {
            \Storage::disk('public')->delete($activity->assignment_letter_path);
            $activity->update(['assignment_letter_path' => null]);
            return redirect()->back()->with('success', 'Surat Tugas berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function deleteAttachment(Activity $activity)
    {
        if (!auth()->user()->canManageActivities()) {
             abort(403, 'Anda tidak memiliki hak akses untuk menghapus lampiran.');
        }

        if ($activity->attachment_path) {
            \Storage::disk('public')->delete($activity->attachment_path);
            $activity->update(['attachment_path' => null]);
            return redirect()->back()->with('success', 'Surat Undangan berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function uploadMaterial(Request $request, Activity $activity)
    {
        if (!auth()->user()->canManagePostActivity()) {
             abort(403, 'Anda tidak memiliki hak akses untuk mengupload materi.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'file_path' => 'required|file|max:20480', // 20MB
        ]);

        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('materials', 'public');
            $activity->materials()->create([
                'title' => $request->title,
                'file_path' => $path,
            ]);
        }

        return redirect()->back()->with('success', 'Bahan materi berhasil ditambahkan.');
    }

    public function deleteMaterial(\App\Models\ActivityMaterial $material)
    {
        if (\Storage::disk('public')->exists($material->file_path)) {
            \Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return redirect()->back()->with('success', 'Bahan materi berhasil dihapus.');
    }

    public function uploadDocumentation(Request $request, Activity $activity)
    {
        $request->validate([
            'file_path' => 'required',
            'file_path.*' => 'file|image|max:10240', // 10MB Images
        ]);

        $currentCount = $activity->documentations()->count();
        $newCount = is_array($request->file('file_path')) ? count($request->file('file_path')) : 1;

        if (($currentCount + $newCount) > 4) {
            return redirect()->back()->with('error', 'Maksimal total 4 foto dokumentasi per kegiatan.');
        }

        if ($request->hasFile('file_path')) {
            $activityName = \Illuminate\Support\Str::slug($activity->name);
            foreach($request->file('file_path') as $index => $file) {
                // Generate filename: Dokumentasi - Activity Name - Timestamp - Index.ext
                $extension = $file->getClientOriginalExtension();
                $filename = "Dokumentasi_{$activityName}_" . time() . "_{$index}.{$extension}";
                
                $path = $file->storeAs("activity_documentations/{$activity->id}", $filename, 'public');
                
                $activity->documentations()->create([
                    'file_path' => $path,
                    // Optional: 'caption' => '...' 
                ]);
            }
        }

        return redirect()->back()->with('success', 'Dokumentasi berhasil ditambahkan.');
    }

    public function deleteDocumentation(\App\Models\ActivityDocumentation $documentation)
    {
        if (!auth()->user()->canManagePostActivity()) {
             abort(403, 'Anda tidak memiliki hak akses untuk menghapus dokumentasi.');
        }

        if (\Storage::disk('public')->exists($documentation->file_path)) {
            \Storage::disk('public')->delete($documentation->file_path);
        }
        $documentation->delete();
        return redirect()->back()->with('success', 'Dokumentasi berhasil dihapus.');
    }

    public function create(Request $request)
    {
        $date = $request->query('date');
        
        // Fetch All Relevant Users for Disposition
        // Roles: Dewan, DJSN, TA
        $targetRoles = ['Dewan', 'DJSN', 'TA'];
        
        $users = User::whereIn('role', $targetRoles)
                     ->orderBy('order') // Use the hierarchical order
                     ->get();
        
        $dewanUsers = $users->groupBy(function($user) {
            // Group 1: Dewan, TA, and Commission-based Groups
            if (in_array($user->role, ['Dewan', 'TA'])) {
                $divisi = strtolower($user->divisi ?? '');
                
                if (str_contains($divisi, 'ketua djsn')) {
                    return 'Ketua DJSN';
                }
                if (str_contains($divisi, 'pme') || str_contains($divisi, 'monitoring')) {
                    return 'Komisi PME';
                }
                if (str_contains($divisi, 'komjakum') || str_contains($divisi, 'kebijakan')) {
                    return 'Komjakum';
                }
                
                // Dynamic Grouping
                if (str_contains($divisi, 'komisi')) {
                    return ucwords($user->divisi);
                }

                if ($user->role === 'Dewan') return 'Anggota Dewan Lainnya';
                // TA Fallback
                return 'Tenaga Ahli Lainnya'; 
            }
            
            // Group 2: Roles separation
            return 'Sekretariat DJSN';
        });

        // Sort Groups: Ketua DJSN -> PME -> Komjakum -> Lainnya -> Sekretariat -> TU -> Persidangan -> Umum
        $groupOrder = [
            'Ketua DJSN' => 1,
            'Komisi PME' => 2,
            'Komjakum' => 3,
            'Anggota Dewan Lainnya' => 4,
            'Sekretariat DJSN' => 5,
            'Tata Usaha' => 6,
            'Persidangan' => 7,
            'Bagian Umum' => 8
        ];

        $dewanUsers = $dewanUsers->sortBy(function($items, $key) use ($groupOrder) {
             return $groupOrder[$key] ?? 99;
        });

        return view('activities.create', compact('date', 'dewanUsers'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canManageActivities()) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuat kegiatan.');
        }

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
            // 'pic' removed from validation as it is auto-generated for internal
            'pic_external' => 'nullable|string',
            'narasumber' => 'nullable|array', // Validasi Narasumber
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

        // Handle PIC Logic
        if ($validated['type'] == 'external') {
            // External: Use manual input if provided
            if ($request->filled('pic_external')) {
                $validated['pic'] = [$request->pic_external];
            } else {
                $validated['pic'] = [];
            }
            // Handle Narasumber for External
            $validated['narasumber'] = $request->narasumber ?? [];
        } else {
            // Internal: Auto-derive from disposition_to
            $validated['pic'] = $this->deriveUnitKerjaFromDisposition($validated['disposition_to'] ?? []);
            // Internal doesn't have Narasumber
            $validated['narasumber'] = [];
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
        // Load users referenced in disposition_to to determine their groups
        $dispoNames = $activity->disposition_to ?? [];
        $dispositionUsers = User::whereIn('name', $dispoNames)->orderBy('order', 'asc')->get();
        
        // Reuse the logic from create/edit
        $groupedDisposition = $dispositionUsers->groupBy(function($user) {
             if (in_array($user->role, ['Dewan', 'TA'])) {
                $divisi = strtolower($user->divisi ?? '');
                if (str_contains($divisi, 'ketua djsn')) return 'Ketua DJSN';
                if (str_contains($divisi, 'pme') || str_contains($divisi, 'monitoring')) return 'Komisi PME';
                if (str_contains($divisi, 'komjakum') || str_contains($divisi, 'kebijakan')) return 'Komjakum';
                if (str_contains($divisi, 'komisi')) return ucwords($user->divisi);
                
                if ($user->role === 'Dewan') return 'Anggota Dewan Lainnya';
                return 'Tenaga Ahli Lainnya';
            }
            return 'Sekretariat DJSN';
        });

        // Sort Groups
        $groupOrder = [
            'Ketua DJSN' => 1, 'Komisi PME' => 2, 'Komjakum' => 3, 'Anggota Dewan Lainnya' => 4,
            'Sekretariat DJSN' => 5, 'Tata Usaha' => 6, 'Persidangan' => 7, 'Bagian Umum' => 8
        ];
        $groupedDisposition = $groupedDisposition->sortBy(function($items, $key) use ($groupOrder) {
             return $groupOrder[$key] ?? 99;
        });

        // Filter Sekretariat DJSN to only show 'DJSN' role (Sekretaris) 
        if ($groupedDisposition->has('Sekretariat DJSN')) {
            $groupedDisposition['Sekretariat DJSN'] = $groupedDisposition['Sekretariat DJSN']->filter(function($user) {
                // Only show Sekretaris DJSN (Imron Rosadi)
                return $user->name === 'Imron Rosadi';
            });
            // If empty (e.g. only staff were selected previously), we can decide to keep empty or unset. 
            // Keeping it consistent with "Only Secretary should appear".
        }

        return view('activities.show', compact('activity', 'groupedDisposition'));
    }



    public function edit(Activity $activity)
    {
        $targetRoles = ['Dewan', 'DJSN', 'TA'];
        
        $users = User::whereIn('role', $targetRoles)
                     ->orderBy('order')
                     ->get();
        
        $dewanUsers = $users->groupBy(function($user) {
             // Group 1: Dewan Specific Groups
             if (in_array($user->role, ['Dewan', 'TA'])) {
                $divisi = strtolower($user->divisi ?? '');
                
                if (str_contains($divisi, 'ketua djsn')) {
                    return 'Ketua DJSN';
                }
                if (str_contains($divisi, 'pme') || str_contains($divisi, 'monitoring')) {
                    return 'Komisi PME';
                }
                if (str_contains($divisi, 'komjakum') || str_contains($divisi, 'kebijakan')) {
                    return 'Komjakum';
                }

                // Dynamic Grouping
                if (str_contains($divisi, 'komisi')) {
                    return ucwords($user->divisi);
                }

                if ($user->role === 'Dewan') return 'Anggota Dewan Lainnya';
                return 'Tenaga Ahli Lainnya';
            }
            
            // Group 2: Roles separation
            return 'Sekretariat DJSN';
        });

        // Sort Groups
        $groupOrder = [
            'Ketua DJSN' => 1,
            'Komisi PME' => 2,
            'Komjakum' => 3,
            'Anggota Dewan Lainnya' => 4,
            'Sekretariat DJSN' => 5,
            'Tata Usaha' => 6,
            'Persidangan' => 7,
            'Bagian Umum' => 8
        ];

        $dewanUsers = $dewanUsers->sortBy(function($items, $key) use ($groupOrder) {
             return $groupOrder[$key] ?? 99;
        });

        return view('activities.edit', compact('activity', 'dewanUsers'));
    }

    public function update(Request $request, Activity $activity)
    {
        if (!auth()->user()->canManageActivities()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit kegiatan.');
        }

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
            // 'pic' removed from validation
            'pic_external' => 'nullable|string',
            'narasumber' => 'nullable|array', // Validasi Narasumber
            'organizer_name' => 'nullable|string',
            'dispo_note' => 'nullable|string',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'minutes_path' => 'nullable|file|mimes:pdf|max:10240',
            'assignment_letter_path' => 'nullable|file|mimes:pdf|max:10240',
            'summary_content' => 'nullable|string',
        ]);

        if ($request->hasFile('attachment_path')) {
            $file = $request->file('attachment_path');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs("attachments/{$activity->id}", $filename, 'public');
            $validated['attachment_path'] = $path;
        }

        if ($request->hasFile('minutes_path')) {
            $file = $request->file('minutes_path');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs("minutes/{$activity->id}", $filename, 'public');
            $validated['minutes_path'] = $path;
        }

        if ($request->hasFile('assignment_letter_path')) {
            $file = $request->file('assignment_letter_path');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs("assignment_letters/{$activity->id}", $filename, 'public');
            $validated['assignment_letter_path'] = $path;
        }

        // Handle PIC Logic
        $type = $request->input('activity_type', $activity->type);
        
        if ($type == 'external') {
            if ($request->filled('pic_external')) {
                $validated['pic'] = [$request->pic_external];
            } else {
                $validated['pic'] = []; // Clear if empty
            }
            // Handle Narasumber for External
            $validated['narasumber'] = $request->narasumber ?? [];
        } else {
             // Internal: Auto-derive, preserving existing if needed or recalculating
             // Re-calculate based on current disposition_to input
             $validated['pic'] = $this->deriveUnitKerjaFromDisposition($validated['disposition_to'] ?? []);
             // Internal doesn't have Narasumber
             $validated['narasumber'] = [];
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

        if (isset($validated['disposition_to']) && !empty($validated['disposition_to'])) {
             if (intval($validated['invitation_status']) === 0) {
                 $validated['invitation_status'] = 1;
             }
        }

        // Record who updated the activity
        $validated['updated_by'] = auth()->id();

        $activity->update($validated);

        // Update Google Calendar Event
        \App\Jobs\SyncGoogleCalendarEvent::dispatch($activity, true);

        return redirect()->route('activities.show', $activity->id)->with('success', 'Kegiatan berhasil diperbarui dan sinkronisasi berjalan di latar belakang.');
    }

    /**
     * Helper to derive Unit Kerja (PIC) from Disposition names.
     */
    private function deriveUnitKerjaFromDisposition(array $dispositionNames)
    {
        $unitKerja = [];
        $councilStructure = Activity::COUNCIL_STRUCTURE;

        // Flatten mapping for easier lookup: Name => Commission
        $nameToCommission = [];
        foreach ($councilStructure as $commission => $members) {
            foreach ($members as $member) {
                $nameToCommission[$member] = $commission;
            }
        }

        foreach ($dispositionNames as $name) {
            if (isset($nameToCommission[$name])) {
                $unitKerja[] = $nameToCommission[$name];
            } else {
                // If not in Council Structure (Dewan), assume Sekretariat DJSN
                // This covers DJSN users and potentially others not explicitly listed in Council Structure
                $unitKerja[] = 'Sekretariat DJSN';
            }
        }

        return array_values(array_unique($unitKerja));
    }

    public function destroy(Activity $activity)
    {
        if (!auth()->user()->canManageActivities()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus kegiatan.');
        }

        // Delete Files
        if ($activity->attachment_path) {
            \Storage::disk('public')->delete($activity->attachment_path);
        }
        if ($activity->minutes_path) {
            \Storage::disk('public')->delete($activity->minutes_path);
        }
        if ($activity->assignment_letter_path) {
            \Storage::disk('public')->delete($activity->assignment_letter_path);
        }

        // Delete Google Calendar Event
        GoogleCalendarService::deleteEvent($activity);

        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil dihapus');
    }

    public function updateSummary(Request $request, Activity $activity)
    {
        if (!auth()->user()->canManagePostActivity()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola ringkasan rapat.');
        }

        $request->validate([
            'summary_content' => 'required|string',
        ]);

        $activity->update([
            'summary_content' => $request->summary_content
        ]);

        return redirect()->route('activities.show', $activity->id)->with('success', 'Berhasil menginput Hasil Rapat Secara Singkat');
    }
    public function updateAdditionalNotes(Request $request, Activity $activity)
    {
        // Additional notes might be open to more roles or specific ones? 
        // User request didn't specify strictly, but 'Persidangan' manages post activity.
        // Let's assume Persidangan or Admin for consistency with post-activity management.
        if (!auth()->user()->canManagePostActivity()) {
             abort(403, 'Anda tidak memiliki hak akses untuk mengelola catatan tambahan.');
        }

        $request->validate([
            'dresscode' => 'nullable|string|max:255',
            'dispo_note' => 'nullable|string',
        ]);

        $activity->update([
            'dresscode' => $request->dresscode,
            'dispo_note' => $request->dispo_note
        ]);

        return redirect()->route('activities.show', $activity->id)->with('success', 'Berhasil memperbarui Catatan Tambahan');
    }
    public function uploadMom(Request $request, Activity $activity)
    {
        if (!auth()->user()->canManagePostActivity()) {
             abort(403, 'Anda tidak memiliki hak akses untuk mengupload MoM.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'file_path' => 'required|file|mimes:pdf,doc,docx|max:20480', // 20MB
        ]);

        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('moms', 'public');
            $activity->moms()->create([
                'title' => $request->title,
                'file_path' => $path,
            ]);
        }

        return redirect()->back()->with('success', 'MoM berhasil ditambahkan.');
    }

    public function deleteMom(\App\Models\ActivityMom $mom)
    {
        if (!auth()->user()->canManagePostActivity()) {
             abort(403, 'Anda tidak memiliki hak akses untuk menghapus MoM.');
        }

        if (\Storage::disk('public')->exists($mom->file_path)) {
            \Storage::disk('public')->delete($mom->file_path);
        }
        $mom->delete();
        return redirect()->back()->with('success', 'MoM berhasil dihapus.');
    }
}
