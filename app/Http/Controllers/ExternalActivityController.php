<?php

namespace App\Http\Controllers;

use App\Models\ExternalActivity;
use Illuminate\Http\Request;

class ExternalActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        // Fetch activities ordered by date
        $activities = ExternalActivity::orderBy('date_time', 'desc')->get();

        // Group by Month-Year for the "Grouped header" effect
        $groupedActivities = $activities->groupBy(function($item) {
            return $item->date_time->format('F Y');
        });

        return view('external.index', compact('groupedActivities'));
    }

    public function create()
    {
        return view('activities.create', ['type' => 'external']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
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

        ExternalActivity::create($validated);

        return redirect()->route('external-activities.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function edit(ExternalActivity $externalActivity)
    {
        return view('activities.create', [
            'type' => 'external',
            'activity' => $externalActivity
        ]);
    }

    public function update(Request $request, ExternalActivity $externalActivity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
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

        $externalActivity->update($validated);

        return redirect()->route('external-activities.index')->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy(ExternalActivity $externalActivity)
    {
        $externalActivity->delete();
        return redirect()->route('external-activities.index')->with('success', 'Kegiatan berhasil dihapus');
    }
}
