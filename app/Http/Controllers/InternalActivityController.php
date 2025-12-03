<?php

namespace App\Http\Controllers;

use App\Models\InternalActivity;
use Illuminate\Http\Request;

class InternalActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        // Fetch activities ordered by date
        $activities = InternalActivity::orderBy('date_time', 'desc')->get();

        // Group by Month-Year
        $groupedActivities = $activities->groupBy(function($item) {
            return $item->date_time->format('F Y');
        });

        return view('internal.index', compact('groupedActivities'));
    }

    public function create()
    {
        return view('activities.create', ['type' => 'internal']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pic' => 'nullable|array',
            'date_time' => 'required|date',
            'status' => 'required|integer',
            'invitation_status' => 'required|integer',
            'location' => 'nullable|string',
            'dispo_note' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('attachment_path')) {
            $path = $request->file('attachment_path')->store('attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        InternalActivity::create($validated);

        return redirect()->route('internal-activities.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function edit(InternalActivity $internalActivity)
    {
        return view('activities.create', [
            'type' => 'internal',
            'activity' => $internalActivity
        ]);
    }

    public function update(Request $request, InternalActivity $internalActivity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pic' => 'nullable|array',
            'date_time' => 'required|date',
            'status' => 'required|integer',
            'invitation_status' => 'required|integer',
            'location' => 'nullable|string',
            'dispo_note' => 'nullable|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('attachment_path')) {
            $path = $request->file('attachment_path')->store('attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        $internalActivity->update($validated);

        return redirect()->route('internal-activities.index')->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy(InternalActivity $internalActivity)
    {
        $internalActivity->delete();
        return redirect()->route('internal-activities.index')->with('success', 'Kegiatan berhasil dihapus');
    }
}
