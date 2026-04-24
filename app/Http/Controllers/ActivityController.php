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
            'create', 'store', 'edit', 'update', 'destroy'
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
        $this->ensureActivityCapability($activity, 'canManagePostActivity', 'Anda tidak memiliki hak akses untuk mengupload notulensi.');

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
        $this->ensureActivityCapability($activity, 'canUploadAssignment', 'Anda tidak memiliki hak akses untuk mengupload surat tugas.');

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
        $this->ensureActivityCapability($activity, 'canManagePostActivity', 'Anda tidak memiliki hak akses untuk menghapus notulensi.');

        if ($activity->minutes_path) {
            \Storage::disk('public')->delete($activity->minutes_path);
            $activity->update(['minutes_path' => null]);
            return redirect()->back()->with('success', 'Notulensi berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function deleteAssignment(Activity $activity)
    {
        $this->ensureActivityCapability($activity, 'canUploadAssignment', 'Anda tidak memiliki hak akses untuk menghapus surat tugas.');

        if ($activity->assignment_letter_path) {
            \Storage::disk('public')->delete($activity->assignment_letter_path);
            $activity->update(['assignment_letter_path' => null]);
            return redirect()->back()->with('success', 'Surat Tugas berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function deleteAttachment(Activity $activity)
    {
        $this->ensureActivityCapability($activity, 'canManageActivities', 'Anda tidak memiliki hak akses untuk menghapus lampiran.');

        if ($activity->attachment_path) {
            \Storage::disk('public')->delete($activity->attachment_path);
            $activity->update(['attachment_path' => null]);
            return redirect()->back()->with('success', 'Surat Undangan berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function uploadMaterial(Request $request, Activity $activity)
    {
        $this->ensureActivityCapability($activity, 'canManagePostActivity', 'Anda tidak memiliki hak akses untuk mengupload materi.');

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

            if ($activity->has_no_materials) {
                $activity->update(['has_no_materials' => false]);
            }
        }

        return redirect()->back()->with('success', 'Bahan materi berhasil ditambahkan.');
    }

    public function deleteMaterial(\App\Models\ActivityMaterial $material)
    {
        $this->ensureActivityCapability($material->activity, 'canManagePostActivity', 'Anda tidak memiliki hak akses untuk menghapus materi.');

        if (\Storage::disk('public')->exists($material->file_path)) {
            \Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return redirect()->back()->with('success', 'Bahan materi berhasil dihapus.');
    }

    public function uploadDocumentation(Request $request, Activity $activity)
    {
        $this->ensureActivityCapability($activity, 'canManageDocumentation', 'Anda tidak memiliki hak akses untuk mengupload dokumentasi.');

        $request->validate([
            'file_path' => 'required',
            'file_path.*' => 'file|image|max:10240', // 10MB Images
        ]);

        $currentCount = $activity->documentations()->count();
        $newCount = is_array($request->file('file_path')) ? count($request->file('file_path')) : 1;

        $totalCount = $currentCount + $newCount;

        if ($totalCount > Activity::DOCUMENTATION_MAX_COUNT) {
            return redirect()->back()->with('error', 'Total dokumentasi tidak boleh melebihi ' . Activity::DOCUMENTATION_MAX_COUNT . ' foto (Saat ini: ' . $currentCount . ' foto).');
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

        $message = 'Dokumentasi berhasil ditambahkan.';

        if ($totalCount < Activity::DOCUMENTATION_MIN_COUNT) {
            $message .= ' Saat ini baru ' . $totalCount . ' foto. Lengkapi minimal ' . Activity::DOCUMENTATION_MIN_COUNT . ' foto.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function deleteDocumentation(\App\Models\ActivityDocumentation $documentation)
    {
        $this->ensureActivityCapability($documentation->activity, 'canManageDocumentation', 'Anda tidak memiliki hak akses untuk menghapus dokumentasi.');

        if (\Storage::disk('public')->exists($documentation->file_path)) {
            \Storage::disk('public')->delete($documentation->file_path);
        }
        $documentation->delete();
        return redirect()->back()->with('success', 'Dokumentasi berhasil dihapus.');
    }

    public function create(Request $request)
    {
        $date = $request->query('date');

        $dewanUsers = $this->getDispositionUserGroups();

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
            'start_time' => $this->fiveMinuteTimeRules(),
            'end_time' => $this->fiveMinuteTimeRules(required: false),
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
            'secretary_disposition_status' => 'nullable|in:disposisi,mengetahui',
            'include_tenaga_ahli' => 'nullable|boolean',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'minutes_path' => 'nullable|file|mimes:pdf|max:10240',
            'summary_content' => 'nullable|string',
            'has_no_materials' => 'nullable|boolean',
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

        $validated['secretary_disposition_status'] = Activity::normalizeSecretaryDispositionStatus(
            $request->input('secretary_disposition_status')
        );
        $validated['include_tenaga_ahli'] = $request->boolean('include_tenaga_ahli');
        $validated['has_no_materials'] = $request->boolean('has_no_materials');

        $activity = Activity::create($validated);

        // Create Google Calendar Event (Asynchronous)
        \App\Jobs\SyncGoogleCalendarEvent::dispatch($activity);
        
        $message = 'Kegiatan berhasil ditambahkan dan sedang diproses untuk integrasi Google Calendar.';

        return redirect()->route('activities.index')->with('success', $message);
    }

    public function show(Activity $activity)
    {
        abort_unless(auth()->user()->canViewActivity($activity), 403, 'Anda tidak memiliki hak akses untuk melihat kegiatan ini.');

        $groupedDisposition = $activity->grouped_disposition_users;

        return view('activities.show', compact('activity', 'groupedDisposition'));
    }



    public function edit(Activity $activity)
    {
        $dewanUsers = $this->getDispositionUserGroups();

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
            'start_time' => $this->fiveMinuteTimeRules(),
            'end_time' => $this->fiveMinuteTimeRules(required: false),
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
            'secretary_disposition_status' => 'nullable|in:disposisi,mengetahui',
            'include_tenaga_ahli' => 'nullable|boolean',
            'disposition_to' => 'nullable|array',
            'dresscode' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'minutes_path' => 'nullable|file|mimes:pdf|max:10240',
            'assignment_letter_path' => 'nullable|file|mimes:pdf|max:10240',
            'summary_content' => 'nullable|string',
            'has_no_materials' => 'nullable|boolean',
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

        $validated['secretary_disposition_status'] = Activity::normalizeSecretaryDispositionStatus(
            $request->input('secretary_disposition_status')
        );
        $validated['include_tenaga_ahli'] = $request->boolean('include_tenaga_ahli');
        $validated['has_no_materials'] = $request->boolean('has_no_materials');

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
        return Activity::derivePicGroupsFromDispositionNames($dispositionNames);
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
        $this->ensureActivityCapability($activity, 'canManagePostActivity', 'Anda tidak memiliki hak akses untuk mengelola ringkasan rapat.');

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
        $this->ensureActivityCapability($activity, 'canManagePostActivity', 'Anda tidak memiliki hak akses untuk mengelola catatan tambahan.');

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
        $this->ensureActivityCapability($activity, 'canManagePostActivity', 'Anda tidak memiliki hak akses untuk mengupload MoM.');

        $request->validate([
            'title' => 'required|string|max:255',
            'file_path' => 'required|file|mimes:pdf|max:20480', // 20MB
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
        $this->ensureActivityCapability($mom->activity, 'canManagePostActivity', 'Anda tidak memiliki hak akses untuk menghapus MoM.');

        if (\Storage::disk('public')->exists($mom->file_path)) {
            \Storage::disk('public')->delete($mom->file_path);
        }
        $mom->delete();
        return redirect()->back()->with('success', 'MoM berhasil dihapus.');
    }

    private function getDispositionUserGroups()
    {
        $users = User::with(['division', 'position'])
            ->get()
            ->filter(fn (User $user) => $user->canReceiveDisposition())
            ->sortBy(fn (User $user) => $this->getDispositionUserSortKey($user));

        return $users
            ->groupBy(fn (User $user) => $user->disposition_group_label)
            ->sortBy(fn ($items, $key) => $this->getDispositionGroupSortOrder($key, $items));
    }

    private function getDispositionUserSortKey(User $user): string
    {
        return sprintf(
            '%09d-%05d-%s',
            $user->management_sort_order,
            $user->order ?? 99999,
            mb_strtolower($user->name)
        );
    }

    private function getDispositionGroupSortOrder(string $groupName, iterable $items): string
    {
        $groupUsers = collect($items)
            ->filter(fn ($item) => $item instanceof User)
            ->values();

        $groupSortOrder = $groupUsers->min(fn (User $user) => $user->management_sort_order) ?? 999999999;
        $userOrder = $groupUsers->min(fn (User $user) => $user->order ?? 99999) ?? 99999;

        return sprintf(
            '%09d-%05d-%s',
            $groupSortOrder,
            $userOrder,
            mb_strtolower(trim($groupName))
        );
    }

    private function fiveMinuteTimeRules(bool $required = true): array
    {
        $rules = [$required ? 'required' : 'nullable', 'date_format:H:i'];
        $rules[] = function (string $attribute, mixed $value, \Closure $fail): void {
            if (!filled($value)) {
                return;
            }

            if (!$this->isFiveMinuteInterval((string) $value)) {
                $label = $attribute === 'start_time' ? 'Jam mulai' : 'Jam selesai';
                $fail($label . ' harus menggunakan interval kelipatan 5 menit.');
            }
        };

        return $rules;
    }

    private function isFiveMinuteInterval(string $value): bool
    {
        try {
            $time = \Carbon\Carbon::createFromFormat('H:i', $value);
        } catch (\Throwable) {
            return false;
        }

        return ((int) $time->format('i')) % 5 === 0;
    }

    private function ensureActivityCapability(Activity $activity, string $capability, string $message): void
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, $capability) || !$user->{$capability}()) {
            abort(403, $message);
        }

        if (!$user->canViewAllActivities() && !$user->canViewActivity($activity)) {
            abort(403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }
    }
}
