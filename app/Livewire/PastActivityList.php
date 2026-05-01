<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use App\Models\ActivityMom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PastActivityList extends Component
{
    use WithFileUploads, WithPagination;

    public $perPage = 10;

    public function updatingPerPage()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

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

    public function updatingSearch()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function updatingMonth()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function updatingYear()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function updatingPic()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function updatingSortDirection()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function updatingPaginators($page, $pageName)
    {
        $this->resetSelectionState();
    }

    public function updatedSelectAll($value)
    {
        $this->ensureCanManageActivities();

        $currentPageIds = $this->getCurrentPageActivityIds();

        if ($value) {
            $this->selected = $currentPageIds;
        } else {
            $this->selected = [];
        }

        $this->syncSelectAllState($currentPageIds);
    }

    public function updatedSelected()
    {
        $this->selected = $this->normalizeSelectedIds($this->selected);
        $this->syncSelectAllState();
    }

    // Livewire Hooks for File Uploads
    public function updatedMinutesFiles($value, $id)
    {
        $this->ensureCanManagePostActivity();

        $this->validate([
            "minutesFiles.{$id}" => 'file|mimes:pdf|max:10240', // 10MB
        ]);

        $activity = $this->resolveVisibleActivity($id);
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
        $this->ensureCanManageActivities();

        $this->validate([
            "assignmentFiles.{$id}" => 'file|mimes:pdf|max:10240', // 10MB
        ]);

        $activity = $this->resolveVisibleActivity($id);
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
        $this->ensureCanManageActivities();

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
        $this->ensureCanManageActivities();

        $activity = $this->resolveVisibleActivity($id);
        if ($activity) {
            $this->deletedIds = [$id];
            $activity->delete(); // Soft Delete
            // No alert, handled by frontend toast
        }
    }

    public function restoreDeleted()
    {
        $this->ensureCanManageActivities();

        if (!empty($this->deletedIds)) {
            Activity::withTrashed()
                ->whereIn('id', $this->deletedIds)
                ->get()
                ->each
                ->restore();
            $this->deletedIds = [];
            $this->dispatch('alert', type: 'success', message: 'Penghapusan dibatalkan.');
        }
    }

    public function forceDeleteDeleted()
    {
        $this->ensureCanManageActivities();

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
        $this->ensureCanManagePostActivity();

        $activity = $this->resolveVisibleActivity($id);
        if ($activity && $activity->minutes_path) {
            Storage::disk('public')->delete($activity->minutes_path);
            $activity->update(['minutes_path' => null]);
            $this->dispatch('alert', type: 'success', message: 'Notulensi berhasil dihapus.');
        }
    }

    public function deleteAssignment($id)
    {
        $this->ensureCanManageActivities();

        $activity = $this->resolveVisibleActivity($id);
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
    public $newAssignmentFile = null;
    public $newAttachmentFile = null; 
    public $newMinutesFile = null;
    public $isModalOpen = false;
    public $activeTab = 'attendance'; 

    // Summary Editor
    public $summaryActivityId = null;
    public $summaryContent = '';

    public function openSummaryModal($id)
    {
        $this->isModalOpen = true;
        $this->summaryActivityId = $id;
        $activity = $this->resolveVisibleActivity($id);
        $this->summaryContent = $activity->summary_content ?? '';
        $this->dispatch('open-summary-modal', content: $this->summaryContent);
    }

    public function saveSummary()
    {
        $this->ensureCanManagePostActivity();

        $this->validate([
             'summaryContent' => 'nullable|string'
        ]);

        if ($this->summaryActivityId) {
            $activity = $this->resolveVisibleActivity($this->summaryActivityId);
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
        $activity = $this->resolveVisibleActivity($id);

        $this->isModalOpen = true;
        $this->activeActivityId = $id;
        
        $currentAttendance = $activity->attendance_list ?? [];
        $this->attendanceDetails = $activity->attendance_details ?? []; // Load details
        
        // Split attendance into System Users (Checkboxes) and Staff (Select2)
        // System Users: Dewan & Imron Rosadi (Sekretariat)
        // We need to keep System Users in $this->attendanceData for checkboxes to work
        
        // 1. Identify System Users names
        $systemUserNames = \App\Models\User::with(['division', 'position'])
            ->get()
            ->filter(fn ($user) => $user->canReceiveDisposition())
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
        $this->newMinutesFile = null;
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
        $this->ensureCanManagePostActivity();

        if (!$this->activeActivityId) return;

        $activity = $this->resolveVisibleActivity($this->activeActivityId);
        
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
        $this->ensureCanManageActivities();

        $this->validate([
            'newAssignmentFile' => 'file|mimes:pdf|max:10240',
        ]);

        if ($this->activeActivityId && $this->newAssignmentFile) {
             $activity = $this->resolveVisibleActivity($this->activeActivityId);
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
        $this->ensureCanManageActivities();

        $this->validate([
            'newAttachmentFile' => 'file|mimes:pdf|max:10240',
        ]);

        if ($this->activeActivityId && $this->newAttachmentFile) {
             $activity = $this->resolveVisibleActivity($this->activeActivityId);
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

    public function updatedNewMinutesFile()
    {
        $this->ensureCanManagePostActivity();

        $this->validate([
            'newMinutesFile' => 'file|mimes:pdf|max:10240',
        ]);

        if ($this->activeActivityId && $this->newMinutesFile) {
            $activity = $this->resolveVisibleActivity($this->activeActivityId);
            if ($activity->minutes_path) {
                Storage::disk('public')->delete($activity->minutes_path);
            }

            $originalName = $this->newMinutesFile->getClientOriginalName();
            $path = $this->newMinutesFile->storeAs("minutes/{$this->activeActivityId}", $originalName, 'public');

            $activity->update(['minutes_path' => $path]);

            if (file_exists($this->newMinutesFile->getRealPath())) {
                @unlink($this->newMinutesFile->getRealPath());
            }

            $this->activeTab = 'letter';
            $this->dispatch('alert', type: 'success', message: 'Notulensi berhasil diupload.');
            $this->newMinutesFile = null;
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
         $this->ensureCanManageActivities();

         if ($this->activeActivityId) {
            $activity = $this->resolveVisibleActivity($this->activeActivityId);
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
         $this->ensureCanManagePostActivity();

         if ($this->activeActivityId) {
            $activity = $this->resolveVisibleActivity($this->activeActivityId);
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
    public $hasNoMaterials = false;

    public function openMaterialModal($id)
    {
        $activity = $this->resolveVisibleActivity($id);

        $this->isModalOpen = true;
        $this->activeActivityId = $id;
        $this->loadMaterials($id);
        $this->hasNoMaterials = (bool) ($activity?->has_no_materials);
        $this->newMaterialTitle = '';
        $this->newMaterialFile = null;
        $this->dispatch('open-material-modal');
    }

    public function loadMaterials($id)
    {
        $activity = $this->resolveVisibleActivity($id);
        $this->materialList = $activity ? $activity->materials()->get() : [];
    }

    public function toggleNoMaterialStatus()
    {
        $this->ensureCanManagePostActivity();

        if (!$this->activeActivityId) {
            return;
        }

        $activity = $this->resolveVisibleActivity($this->activeActivityId);
        if (!$activity) {
            return;
        }

        $nextValue = !$this->hasNoMaterials;

        if ($nextValue && $activity->materials()->exists()) {
            $this->dispatch('alert', type: 'warning', message: 'Hapus semua file bahan materi terlebih dahulu sebelum menandai kegiatan tidak memiliki bahan materi.');

            return;
        }

        $activity->update([
            'has_no_materials' => $nextValue,
        ]);

        $this->hasNoMaterials = $nextValue;

        $this->dispatch(
            'alert',
            type: 'success',
            message: $nextValue
                ? 'Kegiatan ditandai tidak memiliki bahan materi.'
                : 'Penanda bahan materi berhasil dihapus.'
        );
    }

    public function saveMaterial()
    {
        $this->ensureCanManagePostActivity();

        $this->validate([
            'newMaterialTitle' => 'required|string|max:255',
            'newMaterialFile' => 'required|file|max:20480', // 20MB
        ]);

        if ($this->activeActivityId) {
            $activity = $this->resolveVisibleActivity($this->activeActivityId);

            if (!$activity) {
                return;
            }

            $filename = $activity->nextMaterialFilename($this->newMaterialFile->getClientOriginalExtension());
            $path = $this->newMaterialFile->storeAs($activity->materialStorageDirectory(), $filename, 'public');
            
            \App\Models\ActivityMaterial::create([
                'activity_id' => $this->activeActivityId,
                'title' => $this->newMaterialTitle,
                'file_path' => $path
            ]);

            // Clean up temp file
            if (file_exists($this->newMaterialFile->getRealPath())) {
                @unlink($this->newMaterialFile->getRealPath());
            }

            if ($activity->has_no_materials) {
                $activity->update(['has_no_materials' => false]);
            }

            $this->dispatch('alert', type: 'success', message: 'Bahan materi berhasil ditambahkan.');
            
            // Reset fields and reload
            $this->hasNoMaterials = false;
            $this->newMaterialTitle = '';
            $this->newMaterialFile = null;
            $this->loadMaterials($this->activeActivityId);
        }
    }

    public function deleteMaterial($materialId)
    {
        $this->ensureCanManagePostActivity();

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
    public $editingMomId = null;
    public $editingMomTitle = '';

    public function openMomModal($id)
    {
        $this->resolveVisibleActivity($id);

        $this->isModalOpen = true;
        $this->activeActivityId = $id;
        $this->loadMoms($id);
        $this->resetMomUploadForm();
        $this->cancelEditingMom();
        $this->dispatch('open-mom-modal');
    }

    public function loadMoms($id)
    {
        $activity = $this->resolveVisibleActivity($id);
        $this->momList = $activity ? $activity->moms()->latest('created_at')->get() : [];
    }

    public function updatedNewMomFile()
    {
        $this->ensureCanManagePostActivity();

        $this->validate([
            'newMomFile' => 'required|file|mimes:pdf|max:20480',
        ]);
    }

    public function saveMom()
    {
        $this->ensureCanManagePostActivity();

        $this->validate([
            'newMomTitle' => 'required|string|max:255',
            'newMomFile' => 'required',
        ]);

        if (!is_object($this->newMomFile) || !method_exists($this->newMomFile, 'storeAs')) {
            $this->addError('newMomFile', 'File MoM tidak valid. Silakan upload ulang.');

            return;
        }

        if ($this->activeActivityId) {
            $activity = $this->resolveVisibleActivity($this->activeActivityId);
            $title = trim((string) $this->newMomTitle);
            $originalName = $this->newMomFile->getClientOriginalName();
            $path = $this->newMomFile->storeAs("activity_moms/{$this->activeActivityId}", $originalName, 'public');
            
            $mom = ActivityMom::create([
                'activity_id' => $this->activeActivityId,
                'title' => $title,
                'file_path' => $path
            ]);

            // Clean up temp file
            if (file_exists($this->newMomFile->getRealPath())) {
                @unlink($this->newMomFile->getRealPath());
            }

            $this->dispatch('alert', type: 'success', message: 'MoM berhasil ditambahkan.');
            
            // Reset form and enter edit mode immediately on the newest row.
            $this->resetMomUploadForm();
            $this->editingMomId = $mom->id;
            $this->editingMomTitle = $mom->title;
            $this->loadMoms($activity->id);
        }
    }

    public function startEditingMom($momId)
    {
        $this->ensureCanManagePostActivity();

        $mom = $this->resolveActiveMom($momId);
        $this->editingMomId = $mom->id;
        $this->editingMomTitle = $mom->title;
        $this->resetValidation('editingMomTitle');
    }

    public function cancelEditingMom()
    {
        $this->editingMomId = null;
        $this->editingMomTitle = '';
        $this->resetValidation('editingMomTitle');
    }

    public function updateMom()
    {
        $this->ensureCanManagePostActivity();

        if (!$this->editingMomId) {
            return;
        }

        $this->validate([
            'editingMomTitle' => 'required|string|max:255',
        ]);

        $mom = $this->resolveActiveMom($this->editingMomId);
        $mom->update([
            'title' => trim((string) $this->editingMomTitle),
        ]);

        $this->loadMoms($this->activeActivityId);
        $this->cancelEditingMom();
        $this->dispatch('alert', type: 'success', message: 'Judul MoM berhasil diperbarui.');
    }

    public function deleteMom($momId)
    {
        $this->ensureCanManagePostActivity();

        $mom = $this->resolveActiveMom($momId);
        if ($mom) {
            Storage::disk('public')->delete($mom->file_path);
            if ($this->editingMomId === $mom->id) {
                $this->cancelEditingMom();
            }
            $mom->delete();
            $this->dispatch('alert', type: 'success', message: 'MoM berhasil dihapus.');
            $this->loadMoms($this->activeActivityId);
        }
    }

    public function openDocumentationModal($id)
    {
        $this->resolveVisibleActivity($id);

        $this->isModalOpen = true;
        $this->activeActivityId = $id;
        $this->documentationPhotos = [];
        $this->dispatch('open-documentation-modal');
    }

    public function updatedDocumentationPhotos()
    {
        $this->ensureCanManageDocumentation();

        $this->validate([
            'documentationPhotos.*' => 'image|max:5120', 
        ]);

        if ($this->activeActivityId) {
            $activity = $this->resolveVisibleActivity($this->activeActivityId);
            $currentCount = $activity->documentations()->count();
            $newCount = count($this->documentationPhotos);
            $totalCount = $currentCount + $newCount;

            if ($totalCount > Activity::DOCUMENTATION_MAX_COUNT) {
                $this->addError('documentationPhotos', "Total dokumentasi tidak boleh melebihi " . Activity::DOCUMENTATION_MAX_COUNT . " foto (Saat ini: {$currentCount} foto).");
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
            $message = 'Foto dokumentasi berhasil diupload.';

            if ($totalCount < Activity::DOCUMENTATION_MIN_COUNT) {
                $message .= ' Saat ini baru ' . $totalCount . ' foto. Lengkapi minimal ' . Activity::DOCUMENTATION_MIN_COUNT . ' foto.';
            }

            $this->dispatch('alert', type: 'success', message: $message);
        }
    }

    public function deleteDocumentationFile($docId)
    {
        $this->ensureCanManageDocumentation();

        $doc = \App\Models\ActivityDocumentation::find($docId);
        if ($doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
            $this->dispatch('alert', type: 'success', message: 'Foto berhasil dihapus.');
        }
    }

    public function render()
    {
        $activities = $this->getFilteredActivitiesQuery()
            ->with(['materials', 'moms', 'documentations'])
            ->paginate($this->perPage);

        // Group the records on the current page
        $groupedActivities = $activities->getCollection()->groupBy(function($item) {
            return $item->date_time->isoFormat('MMMM Y');
        });

        // ... (Dewan Users logic remains same)

        $dewanUsers = \App\Models\User::with(['division', 'position'])
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->filter(fn ($user) => $user->canReceiveDisposition())
            ->groupBy(fn ($user) => $user->disposition_group_label)
            ->sortBy(function ($users, $key) {
                return Activity::getInternalPicPriority($key);
            });

        $attendanceSecretaryGroups = $dewanUsers->filter(
            fn (Collection $users) => $this->isSecretaryDispositionGroup($users)
        );

        $attendanceDewanGroups = $dewanUsers->reject(
            fn (Collection $users) => $this->isSecretaryDispositionGroup($users)
        );

        return view('livewire.past-activity-list', [
            'groupedActivities' => $groupedActivities,
            'activities' => $activities, // Pass paginator
            'attendanceDewanGroups' => $attendanceDewanGroups,
            'attendanceSecretaryGroups' => $attendanceSecretaryGroups,
            'staffSekretariat' => \App\Models\Staff::where('type', 'sekretariat')->orderBy('name')->get(),
            'staffTA' => \App\Models\Staff::where('type', 'ta')->orderBy('name')->get()
        ]);
    }

    private function isSecretaryDispositionGroup(Collection $users): bool
    {
        $sampleUser = $users->first();

        if (!$sampleUser instanceof \App\Models\User) {
            return false;
        }

        return $sampleUser->division?->structure_group === \App\Models\Division::STRUCTURE_GROUP_SECRETARY
            || $sampleUser->isSetDjsn();
    }

    private function getFilteredActivitiesQuery(): Builder
    {
        $query = Activity::query();

        if (auth()->check()) {
            $query->visibleToUser(auth()->user());
        }

        $query->where('start_date', '<', now()->startOfDay());

        if ($this->year) {
            $query->whereYear('start_date', $this->year);
        }

        if ($this->month) {
            $query->whereMonth('start_date', $this->month);
        }

        $query->orderBy('start_date', $this->sortDirection)
              ->orderBy('start_time', 'desc');

        if ($this->type && in_array($this->type, ['external', 'internal'])) {
            $query->where('type', $this->type);
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($this->pic) {
            $pic = $this->pic;
            $query->where(function($q) use ($pic) {
                $q->whereJsonContains('pic', $pic)
                  ->orWhere('pic', 'like', "%{$pic}%");
            });
        }

        return $query;
    }

    private function getCurrentPageActivityIds(): array
    {
        return $this->getFilteredActivitiesQuery()
            ->paginate($this->perPage, ['activities.id'], 'page', $this->getPage())
            ->getCollection()
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->values()
            ->all();
    }

    private function syncSelectAllState(?array $currentPageIds = null): void
    {
        $currentPageIds ??= $this->getCurrentPageActivityIds();

        $this->selectAll = !empty($currentPageIds)
            && empty(array_diff($currentPageIds, $this->normalizeSelectedIds($this->selected)));
    }

    private function resetSelectionState(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    private function normalizeSelectedIds(array $selected): array
    {
        return array_values(array_unique(array_map('strval', $selected)));
    }

    private function resolveVisibleActivity($id): Activity
    {
        $activity = Activity::findOrFail($id);
        abort_unless(auth()->user()->canViewActivity($activity), 403);

        return $activity;
    }

    private function resolveActiveMom($momId): ActivityMom
    {
        abort_unless($this->activeActivityId, 404);

        $activity = $this->resolveVisibleActivity($this->activeActivityId);

        return $activity->moms()->findOrFail($momId);
    }

    private function resetMomUploadForm(): void
    {
        $this->newMomTitle = '';
        $this->newMomFile = null;
        $this->resetValidation(['newMomTitle', 'newMomFile']);
    }

    private function ensureCanManageActivities(): void
    {
        abort_unless(auth()->user()->canManageActivities(), 403);
    }

    private function ensureCanManagePostActivity(): void
    {
        abort_unless(auth()->user()->canManagePostActivity(), 403);
    }

    private function ensureCanManageDocumentation(): void
    {
        abort_unless(auth()->user()->canManageDocumentation(), 403);
    }
}
