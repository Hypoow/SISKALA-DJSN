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
    
    // New Filters
    public $month = '';
    public $year;
    public $pic; // New Property
    public $sortField = 'start_date';

    // Bulk Delete
    public $selected = [];
    public $selectAll = false;
    public $deletedIds = []; // Track deleted IDs for Undo

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

    public function mount()
    {
        $this->year = date('Y');
        $this->month = date('n');

        // Auto-cleanup trash (force delete soft-deleted items immediately on load)
        Activity::pruneTrash(0);
    }

    public function updatedMonth()
    {
        // $this->resetPage(); 
    }

    public function updatedYear()
    {
        // $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = Activity::query();
             // if (auth()->check()) {
             //   $query->visibleToUser(auth()->user()); // Removed per user request
             // }

            $query->where('start_date', '<', now()->startOfDay())
                             ->orderBy('start_date', $this->sortDirection)
                             ->orderBy('start_time', 'desc');

            if ($this->type && in_array($this->type, ['external', 'internal'])) {
                $query->where('type', $this->type);
            }
            if ($this->year) {
                $query->whereYear('start_date', $this->year);
            }
            if ($this->month) {
                $query->whereMonth('start_date', $this->month);
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

            $originalName = $this->minutesFiles[$id]->getClientOriginalName();
            $path = $this->minutesFiles[$id]->storeAs("minutes/{$id}", $originalName, 'public');
            $activity->update(['minutes_path' => $path]);

            // Optional: Manually clean up temp file immediately
            if (file_exists($this->minutesFiles[$id]->getRealPath())) {
                @unlink($this->minutesFiles[$id]->getRealPath());
            }

            $this->dispatch('alert', type: 'success', message: 'Notulensi berhasil diupload.');
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

            $originalName = $this->assignmentFiles[$id]->getClientOriginalName();
            $path = $this->assignmentFiles[$id]->storeAs("assignment_letters/{$id}", $originalName, 'public');
            $activity->update(['assignment_letter_path' => $path]);

            // Clean up temp file
            if (file_exists($this->assignmentFiles[$id]->getRealPath())) {
                @unlink($this->assignmentFiles[$id]->getRealPath());
            }

            $this->dispatch('alert', type: 'success', message: 'Surat Tugas berhasil diupload.');
            unset($this->assignmentFiles[$id]);
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        $activities = Activity::whereIn('id', $this->selected)->get();
        // Store IDs for potential Undo
        $this->deletedIds = $this->selected;
        
        foreach ($activities as $activity) {
            // Soft delete
            $activity->delete();
        }

        $this->selected = [];
        $this->selectAll = false;
        
        // No alert here, handled by frontend toast
    }

    public function delete($id)
    {
        $activity = Activity::find($id);
        if ($activity) {
            $this->deletedIds = [$id];
            $activity->delete(); // Soft Delete
            // No alert, handled by frontend toast
        }
    }

    public function restoreDeleted()
    {
        if (!empty($this->deletedIds)) {
            Activity::withTrashed()->whereIn('id', $this->deletedIds)->restore();
            $this->deletedIds = [];
            $this->dispatch('alert', type: 'success', message: 'Penghapusan dibatalkan.');
        }
    }

    public function forceDeleteDeleted()
    {
        if (!empty($this->deletedIds)) {
            // Force delete will trigger the 'forceDeleting' event in the model to clean up files
            $activities = Activity::withTrashed()->whereIn('id', $this->deletedIds)->get();
            foreach ($activities as $activity) {
                $activity->forceDelete();
            }
            $this->deletedIds = [];
            $this->dispatch('alert', type: 'success', message: 'Kegiatan berhasil dihapus permanen.');
        }
    }

    public function deleteMinutes($id)
    {
        $activity = Activity::find($id);
        if ($activity && $activity->minutes_path) {
            Storage::disk('public')->delete($activity->minutes_path);
            $activity->update(['minutes_path' => null]);
            $this->dispatch('alert', type: 'success', message: 'Notulensi berhasil dihapus.');
        }
    }

    public function deleteAssignment($id)
    {
        $activity = Activity::find($id);
        if ($activity && $activity->assignment_letter_path) {
            Storage::disk('public')->delete($activity->assignment_letter_path);
            $activity->update(['assignment_letter_path' => null]);
            $this->dispatch('alert', type: 'success', message: 'Surat Tugas berhasil dihapus.');
        }
    }

    // State for Modals
    public $activeActivityId = null;
    public $attendanceData = [];
    public $attendanceDetails = []; // New Property
    public $selectedSekretariat = []; // Manual Staff input
    public $selectedTA = []; // Manual Staff input
    public $documentationPhotos = [];
    public $maxPhotos = 4;
    public $newAssignmentFile = null;
    public $newAttachmentFile = null; 
    public $isModalOpen = false;
    public $activeTab = 'attendance'; 

    // Summary Editor
    public $summaryActivityId = null;
    public $summaryContent = '';

    public function openSummaryModal($id)
    {
        $this->isModalOpen = true;
        $this->summaryActivityId = $id;
        $activity = Activity::find($id);
        $this->summaryContent = $activity->summary_content ?? '';
        $this->dispatch('open-summary-modal', content: $this->summaryContent);
    }

    public function saveSummary()
    {
        $this->validate([
             'summaryContent' => 'nullable|string'
        ]);

        if ($this->summaryActivityId) {
            $activity = Activity::find($this->summaryActivityId);
            $activity->update(['summary_content' => $this->summaryContent]);
            
            $this->dispatch('alert', type: 'success', message: 'Ringkasan Rapat berhasil disimpan.');
            $this->dispatch('close-summary-modal');
            $this->isModalOpen = false;
        }
    } 

    public function closeModalState()
    {
        $this->isModalOpen = false;
    }

    public function openAssignmentModal($id)
    {
        $this->isModalOpen = true;
        $this->activeActivityId = $id;
        $activity = Activity::find($id);
        
        $currentAttendance = $activity->attendance_list ?? [];
        $this->attendanceDetails = $activity->attendance_details ?? []; // Load details
        
        // Split attendance into System Users (Checkboxes) and Staff (Select2)
        // System Users: Dewan & Imron Rosadi (Sekretariat)
        // We need to keep System Users in $this->attendanceData for checkboxes to work
        
        // 1. Identify System Users names
        $systemUserNames = \App\Models\User::whereIn('role', ['Dewan'])
                            ->orWhere('name', 'Imron Rosadi')
                            ->pluck('name')
                            ->toArray();
                            
        // 2. Identify Staff names
        $staffSekretariatNames = \App\Models\Staff::where('type', 'sekretariat')->pluck('name')->toArray();
        $staffTANames = \App\Models\Staff::where('type', 'ta')->pluck('name')->toArray();

        $this->attendanceData = [];
        $this->selectedSekretariat = [];
        $this->selectedTA = [];

        foreach ($currentAttendance as $name) {
            if (in_array($name, $systemUserNames)) {
                $this->attendanceData[] = $name;
            } elseif (in_array($name, $staffSekretariatNames)) {
                $this->selectedSekretariat[] = $name;
            } elseif (in_array($name, $staffTANames)) {
                $this->selectedTA[] = $name;
            } else {
                // Unknown/External or removed user? Keep in attendanceData just in case so it's not lost?
                // Or maybe assume it's a checkbox user we missed?
                // Let's check if it matches ANY User, if so keep in attendanceData
                $isUser = \App\Models\User::where('name', $name)->exists();
                if ($isUser) {
                     $this->attendanceData[] = $name;
                } else {
                    // Fallback: put in Manual Sekretariat or just leave it?
                    // Safe bet: Put in attendanceData (checkboxes) if it might be a user, 
                    // but if it's not in the view's list it won't show boxed.
                    // Let's assume standard behavior.
                }
            }
        }
        
        $this->newAssignmentFile = null; 
        $this->newAttachmentFile = null; 
        $this->activeTab = 'attendance';
        $this->dispatch('open-assignment-modal');
        
        // Dispatch event to update Select2
        $this->dispatch('update-staff-select', 
            sekretariat: $this->selectedSekretariat, 
            ta: $this->selectedTA
        );
    }

    public function saveAttendance()
    {
        if (!$this->activeActivityId) return;

        $activity = Activity::find($this->activeActivityId);
        
        // Merge Checkboxes + Manual Inputs
        $finalList = array_merge(
            $this->attendanceData, 
            $this->selectedSekretariat, 
            $this->selectedTA
        );
        $finalList = array_unique($finalList); // Prevent duplicates

        $activity->update([
            'attendance_list' => array_values($finalList),
            'attendance_details' => $this->attendanceDetails // Save details
        ]);
        
        $this->dispatch('alert', type: 'success', message: 'Simpan Kehadiran Berhasil.');
    }

    public function updatedNewAssignmentFile()
    {
        $this->validate([
            'newAssignmentFile' => 'file|mimes:pdf|max:10240',
        ]);

        if ($this->activeActivityId && $this->newAssignmentFile) {
             $activity = Activity::find($this->activeActivityId);
             if ($activity->assignment_letter_path) {
                Storage::disk('public')->delete($activity->assignment_letter_path);
             }
             
             $originalName = $this->newAssignmentFile->getClientOriginalName();
             // Store in a subfolder to prevent name collisions
             $path = $this->newAssignmentFile->storeAs("assignment_letters/{$this->activeActivityId}", $originalName, 'public');
             
             $activity->update(['assignment_letter_path' => $path]);
             
             $this->activeTab = 'letter';
             $this->dispatch('alert', type: 'success', message: 'Surat Tugas berhasil diupload.');
             $this->newAssignmentFile = null;
        }
    }

    public function updatedNewAttachmentFile()
    {
        $this->validate([
            'newAttachmentFile' => 'file|mimes:pdf|max:10240',
        ]);

        if ($this->activeActivityId && $this->newAttachmentFile) {
             $activity = Activity::find($this->activeActivityId);
             if ($activity->attachment_path) {
                // Delete old if exists
                Storage::disk('public')->delete($activity->attachment_path);
             }
             // Store new
             $originalName = $this->newAttachmentFile->getClientOriginalName();
             $path = $this->newAttachmentFile->storeAs("attachments/{$this->activeActivityId}", $originalName, 'public');
             $activity->update(['attachment_path' => $path]);
             
             // Clean up temp file
             if (file_exists($this->newAttachmentFile->getRealPath())) {
                 @unlink($this->newAttachmentFile->getRealPath());
             }

             $this->dispatch('alert', type: 'success', message: 'Surat Undangan berhasil diupload.');
             $this->newAttachmentFile = null;
        }
    }

    public function deleteAssignmentInModal()
    {
         if ($this->activeActivityId) {
            $this->deleteAssignment($this->activeActivityId);
            $this->activeTab = 'letter';
            // Alert dispatched in deleteAssignment
         }
    }

    public function deleteAttachmentInModal()
    {
         if ($this->activeActivityId) {
            $activity = Activity::find($this->activeActivityId);
            if ($activity && $activity->attachment_path) {
                Storage::disk('public')->delete($activity->attachment_path);
                $activity->update(['attachment_path' => null]);
                $this->activeTab = 'letter';
                $this->dispatch('alert', type: 'success', message: 'Surat Undangan berhasil dihapus.');
            }
         }
    }
    public function deleteMinutesInModal()
    {
         if ($this->activeActivityId) {
            $activity = Activity::find($this->activeActivityId);
            if ($activity && $activity->minutes_path) {
                Storage::disk('public')->delete($activity->minutes_path);
                $activity->update(['minutes_path' => null]);
                $this->activeTab = 'letter';
                $this->dispatch('alert', type: 'success', message: 'Notulensi berhasil dihapus.');
            }
         }
    }

    // Materials Management
    public $materialList = [];
    public $newMaterialTitle = '';
    public $newMaterialFile = null;

    public function openMaterialModal($id)
    {
        $this->isModalOpen = true;
        $this->activeActivityId = $id;
        $this->loadMaterials($id);
        $this->newMaterialTitle = '';
        $this->newMaterialFile = null;
        $this->dispatch('open-material-modal');
    }

    public function loadMaterials($id)
    {
        $activity = Activity::find($id);
        $this->materialList = $activity ? $activity->materials()->get() : [];
    }

    public function saveMaterial()
    {
        $this->validate([
            'newMaterialTitle' => 'required|string|max:255',
            'newMaterialFile' => 'required|file|max:20480', // 20MB
        ]);

        if ($this->activeActivityId) {
            $originalName = $this->newMaterialFile->getClientOriginalName();
            $path = $this->newMaterialFile->storeAs("activity_materials/{$this->activeActivityId}", $originalName, 'public');
            
            \App\Models\ActivityMaterial::create([
                'activity_id' => $this->activeActivityId,
                'title' => $this->newMaterialTitle,
                'file_path' => $path
            ]);

            // Clean up temp file
            if (file_exists($this->newMaterialFile->getRealPath())) {
                @unlink($this->newMaterialFile->getRealPath());
            }

            $this->dispatch('alert', type: 'success', message: 'Bahan materi berhasil ditambahkan.');
            
            // Reset fields and reload
            $this->newMaterialTitle = '';
            $this->newMaterialFile = null;
            $this->loadMaterials($this->activeActivityId);
        }
    }

    public function deleteMaterial($materialId)
    {
        $material = \App\Models\ActivityMaterial::find($materialId);
        if ($material) {
            Storage::disk('public')->delete($material->file_path);
            $material->delete();
            $this->dispatch('alert', type: 'success', message: 'Bahan materi berhasil dihapus.');
            $this->loadMaterials($this->activeActivityId);
        }
    }

    // MoM Management (Minutes of Meeting)
    public $momList = [];
    public $newMomTitle = '';
    public $newMomFile = null;

    public function openMomModal($id)
    {
        $this->isModalOpen = true;
        $this->activeActivityId = $id;
        $this->loadMoms($id);
        $this->newMomTitle = '';
        $this->newMomFile = null;
        $this->dispatch('open-mom-modal');
    }

    public function loadMoms($id)
    {
        $activity = Activity::find($id);
        $this->momList = $activity ? $activity->moms()->get() : [];
    }

    public function saveMom()
    {
        $this->validate([
            'newMomTitle' => 'required|string|max:255',
            'newMomFile' => 'required|file|mimes:pdf,doc,docx|max:20480', // 20MB
        ]);

        if ($this->activeActivityId) {
            $originalName = $this->newMomFile->getClientOriginalName();
            $path = $this->newMomFile->storeAs("activity_moms/{$this->activeActivityId}", $originalName, 'public');
            
            \App\Models\ActivityMom::create([
                'activity_id' => $this->activeActivityId,
                'title' => $this->newMomTitle,
                'file_path' => $path
            ]);

            // Clean up temp file
            if (file_exists($this->newMomFile->getRealPath())) {
                @unlink($this->newMomFile->getRealPath());
            }

            $this->dispatch('alert', type: 'success', message: 'MoM berhasil ditambahkan.');
            
            // Reset fields and reload
            $this->newMomTitle = '';
            $this->newMomFile = null;
            $this->loadMoms($this->activeActivityId);
        }
    }

    public function deleteMom($momId)
    {
        $mom = \App\Models\ActivityMom::find($momId);
        if ($mom) {
            Storage::disk('public')->delete($mom->file_path);
            $mom->delete();
            $this->dispatch('alert', type: 'success', message: 'MoM berhasil dihapus.');
            $this->loadMoms($this->activeActivityId);
        }
    }

    public function openDocumentationModal($id)
    {
        $this->isModalOpen = true;
        $this->activeActivityId = $id;
        $this->documentationPhotos = [];
        $this->dispatch('open-documentation-modal');
    }

    public function updatedDocumentationPhotos()
    {
        $this->validate([
            'documentationPhotos.*' => 'image|max:5120', 
        ]);

        if ($this->activeActivityId) {
            $activity = Activity::find($this->activeActivityId);
            $currentCount = $activity->documentations()->count();
            $newCount = count($this->documentationPhotos);

            if (($currentCount + $newCount) > $this->maxPhotos) {
                $this->addError('documentationPhotos', "Maksimal {$this->maxPhotos} foto diperbolehkan.");
                return;
            }

            $activityName = \Illuminate\Support\Str::slug($activity->name);
            foreach ($this->documentationPhotos as $index => $photo) {
                $extension = $photo->getClientOriginalExtension();
                // Naming: Dokumentasi_ActivityName_Time_Index.ext
                $filename = "Dokumentasi_{$activityName}_" . time() . "_{$index}.{$extension}";
                
                $path = $photo->storeAs("activity_documentations/{$this->activeActivityId}", $filename, 'public');
                $activity->documentations()->create(['file_path' => $path]);

                // Clean up temp file
                if (file_exists($photo->getRealPath())) {
                    @unlink($photo->getRealPath());
                }
            }

            $this->documentationPhotos = [];
            $this->dispatch('alert', type: 'success', message: 'Foto Dokumentasi berhasil diupload.');
        }
    }

    public function deleteDocumentationFile($docId)
    {
        $doc = \App\Models\ActivityDocumentation::find($docId);
        if ($doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
            $this->dispatch('alert', type: 'success', message: 'Foto berhasil dihapus.');
        }
    }

    public function render()
    {
        $query = Activity::query();
        
        // Eager load relationships to fix N+1
        $query->with(['materials', 'moms', 'documentations']);
        
        // if (auth()->check()) {
        //    $query->visibleToUser(auth()->user()); // Removed per user request
        // }

        $query->where('start_date', '<', now()->startOfDay());

        // Apply Year Filter
        if ($this->year) {
            $query->whereYear('start_date', $this->year);
        }

        // Apply Month Filter
        if ($this->month) {
            $query->whereMonth('start_date', $this->month);
        }

        $query->orderBy('start_date', $this->sortDirection)
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
        
        if ($this->pic) {
            $query->where(function($q) {
                // If it is stored as JSON array ["Sekretariat DJSN"]
                $q->whereJsonContains('pic', $this->pic)
                  // Or if it was stored as string "Sekretariat DJSN"
                  ->orWhere('pic', 'like', "%{$this->pic}%");
            });
        }

        // Use pagination
        $activities = $query->paginate(15);

        // Group the records on the current page
        $groupedActivities = $activities->getCollection()->groupBy(function($item) {
            return $item->date_time->isoFormat('MMMM Y');
        });

        // ... (Dewan Users logic remains same)

        // Fetch Dewan Users
        $dewanUsersRaw = \App\Models\User::where('role', 'Dewan')
                     ->orderBy('order')
                     ->get();

        // Ensure Sekretariat DJSN is present (Imron Rosadi) even if not role='Dewan'
        $imron = \App\Models\User::where('name', 'Imron Rosadi')->first();
        if ($imron && !$dewanUsersRaw->contains('id', $imron->id)) {
            $dewanUsersRaw->push($imron);
        }

        // Custom Grouping Logic
        $dewanUsers = $dewanUsersRaw->groupBy(function ($user) {
            $divisi = strtoupper($user->divisi ?? '');
            $name = strtoupper($user->name);
            $role = strtoupper($user->role);

            if ($divisi == 'KETUA DJSN') {
                return 'Ketua DJSN';
            }
            
            if (str_contains($divisi, 'PME')) {
                return 'Komisi PME';
            }

            if (str_contains($divisi, 'KOMJAKUM')) {
                return 'Komjakum';
            }

            if (str_contains($divisi, 'SEKRETARI') || $name == 'IMRON ROSADI') {
                return 'Sekretaris DJSN';
            }
            
            if ($role == 'TA') {
                 // Should have been caught by PME/KOMJAKUM check above if divisi is set correctly
                 // If not, fallback
                 return 'Tenaga Ahli Lainnya';
            }

            return 'Anggota Dewan Lainnya'; // Fallback
        });

        // Define specific order for groups
        $groupOrder = ['Ketua DJSN', 'Komisi PME', 'Komjakum', 'Sekretaris DJSN', 'Tenaga Ahli Lainnya'];
        $dewanUsers = $dewanUsers->sortBy(function ($users, $key) use ($groupOrder) {
            $index = array_search($key, $groupOrder);
            return $index === false ? 999 : $index;
        });

        return view('livewire.past-activity-list', [
            'groupedActivities' => $groupedActivities,
            'activities' => $activities, // Pass paginator
            'dewanUsers' => $dewanUsers,
            'staffSekretariat' => \App\Models\Staff::where('type', 'sekretariat')->orderBy('name')->get(),
            'staffTA' => \App\Models\Staff::where('type', 'ta')->orderBy('name')->get()
        ]);
    }
}
