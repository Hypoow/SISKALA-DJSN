<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;

class PastActivityRow extends Component
{
    use WithFileUploads;

    public Activity $activity;
    
    // Upload Properties
    public $minutesFile;
    public $assignmentFile;

    public function mount(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function updatedMinutesFile()
    {
        $this->validate([
            'minutesFile' => 'required|file|mimes:pdf|max:10240', // 10MB Max
        ]);

        $path = $this->minutesFile->store('minutes', 'public');

        // Delete old file if exists
        if ($this->activity->minutes_path) {
            Storage::disk('public')->delete($this->activity->minutes_path);
        }

        $this->activity->update([
            'minutes_path' => $path
        ]);
        
        $this->reset('minutesFile');
        session()->flash('message', 'Notulensi berhasil diupload.');
    }

    public function updatedAssignmentFile()
    {
         $this->validate([
            'assignmentFile' => 'required|file|mimes:pdf|max:10240', // 10MB Max
        ]);

        $path = $this->assignmentFile->store('assignments', 'public');

         // Delete old file if exists
        if ($this->activity->assignment_letter_path) {
             Storage::disk('public')->delete($this->activity->assignment_letter_path);
        }

        $this->activity->update([
            'assignment_letter_path' => $path
        ]);

        $this->reset('assignmentFile');
        session()->flash('message', 'Surat Tugas berhasil diupload.');
    }

    public function deleteFile($type)
    {
        if ($type === 'minutes') {
            if ($this->activity->minutes_path) {
                Storage::disk('public')->delete($this->activity->minutes_path);
                $this->activity->update(['minutes_path' => null]);
                session()->flash('message', 'Notulensi berhasil dihapus.');
            }
        } elseif ($type === 'assignment') {
             if ($this->activity->assignment_letter_path) {
                Storage::disk('public')->delete($this->activity->assignment_letter_path);
                $this->activity->update(['assignment_letter_path' => null]);
                session()->flash('message', 'Surat Tugas berhasil dihapus.');
            }
        }
    }

    public function render()
    {
        return view('livewire.past-activity-row');
    }
}
