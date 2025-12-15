<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;

use Livewire\WithFileUploads;

class PastActivityList extends Component
{
    use WithFileUploads;

    public $search = '';
    public $type = '';
    
    // Bulk Delete
    public $selected = [];
    public $selectAll = false;

    // File Uploads
    public $minutesFiles = [];
    public $assignmentFiles = [];

    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'sortDirection' => ['except' => 'desc']
    ];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = Activity::where('start_date', '<', now()->startOfDay())
                             ->orderBy('start_date', $this->sortDirection)
                             ->orderBy('start_time', 'desc');

            if ($this->type && in_array($this->type, ['external', 'internal'])) {
                $query->where('type', $this->type);
            }
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('location', 'like', "%{$this->search}%");
                });
            }
            $this->selected = $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    // Livewire Hooks for File Uploads
    public function updatedMinutesFiles($value, $id)
    {
        $this->validate([
            "minutesFiles.{$id}" => 'file|mimes:pdf|max:10240', // 10MB
        ]);

        $activity = Activity::find($id);
        if ($activity && isset($this->minutesFiles[$id])) {
            // Delete old file if exists
            if ($activity->minutes_path) {
                Storage::disk('public')->delete($activity->minutes_path);
            }

            $path = $this->minutesFiles[$id]->store('minutes', 'public');
            $activity->update(['minutes_path' => $path]);

            session()->flash('success_upload', 'Notulensi berhasil diupload.');
            // Clear the file input from state to save memory
            unset($this->minutesFiles[$id]);
        }
    }

    public function updatedAssignmentFiles($value, $id)
    {
        $this->validate([
            "assignmentFiles.{$id}" => 'file|mimes:pdf|max:10240', // 10MB
        ]);

        $activity = Activity::find($id);
        if ($activity && isset($this->assignmentFiles[$id])) {
            // Delete old file if exists
            if ($activity->assignment_letter_path) {
                Storage::disk('public')->delete($activity->assignment_letter_path);
            }

            $path = $this->assignmentFiles[$id]->store('assignment_letters', 'public');
            $activity->update(['assignment_letter_path' => $path]);

            session()->flash('success_upload', 'Surat Tugas berhasil diupload.');
            unset($this->assignmentFiles[$id]);
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        $activities = Activity::whereIn('id', $this->selected)->get();
        foreach ($activities as $activity) {
            if ($activity->minutes_path) {
                Storage::disk('public')->delete($activity->minutes_path);
            }
            if ($activity->assignment_letter_path) {
                Storage::disk('public')->delete($activity->assignment_letter_path);
            }
            if ($activity->attachment_path) {
                Storage::disk('public')->delete($activity->attachment_path);
            }
            $activity->delete();
        }

        $this->selected = [];
        $this->selectAll = false;
        
        session()->flash('success', 'Kegiatan terpilih berhasil dihapus.');
    }

    public function deleteMinutes($id)
    {
        $activity = Activity::find($id);
        if ($activity && $activity->minutes_path) {
            Storage::disk('public')->delete($activity->minutes_path);
            $activity->update(['minutes_path' => null]);
            session()->flash('success_upload', 'Notulensi berhasil dihapus.');
        }
    }

    public function deleteAssignment($id)
    {
        $activity = Activity::find($id);
        if ($activity && $activity->assignment_letter_path) {
            Storage::disk('public')->delete($activity->assignment_letter_path);
            $activity->update(['assignment_letter_path' => null]);
            session()->flash('success_upload', 'Surat Tugas berhasil dihapus.');
        }
    }

    public function render()
    {
        $query = Activity::where('start_date', '<', now()->startOfDay())
                         ->orderBy('start_date', $this->sortDirection)
                         ->orderBy('start_time', 'desc');

        if ($this->type && in_array($this->type, ['external', 'internal'])) {
            $query->where('type', $this->type);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%");
            });
        }

        $activities = $query->get();

        $groupedActivities = $activities->groupBy(function($item) {
            return $item->date_time->isoFormat('MMMM Y');
        });

        return view('livewire.past-activity-list', [
            'groupedActivities' => $groupedActivities
        ]);
    }
}
