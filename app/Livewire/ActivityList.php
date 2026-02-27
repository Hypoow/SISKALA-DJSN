<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination;

    public function getPicColor($picName)
    {
        // Define specific colors for known Commissions
        $map = [
            'Komjakum' => 'komjakum',
            'Komisi PME' => 'pme',
            'PME' => 'pme',
            'Sekretariat DJSN' => 'sekretariat',
            'Anggota DJSN' => 'djsn',
            'Ketua DJSN' => 'ketua',
        ];

        if (str_contains(strtoupper($picName), 'PME')) {
            return 'pme';
        }

        if (isset($map[$picName])) {
            return $map[$picName];
        }

        return 'primary'; // Default fallback
    }

    public $search = '';
    public $type = '';

    public $sortDirection = 'asc';

    public function mount()
    {
        // Auto-cleanup trash (force delete soft-deleted items immediately on load)
        Activity::pruneTrash(0);
    }

    public function getUrgentActivitiesProperty()
    {
        return Activity::whereBetween('start_date', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
                       ->where('status', '!=', Activity::STATUS_CANCELLED) // Assuming 3 is cancelled, but let's check constants or use logic
                       ->where(function($q) {
                           $q->whereNull('disposition_to')
                             ->orWhere('disposition_to', '[]'); // For JSON/Array columns
                       })
                       ->get();
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'sortDirection' => ['except' => 'asc']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    // Bulk Delete Logic
    public $selected = [];
    public $selectAll = false;
    public $deletedIds = []; // Track deleted IDs for Undo

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all on current page (approximate since we don't have easy access to the paginator here without query)
            // Or simpler: We can just let the view handle "Select All" if we pass the IDs, but wire:model on header is easier.
            // Let's re-run the query to get IDs. 
            $query = Activity::where('start_date', '>=', now()->startOfDay())
                             ->orderBy('start_date', $this->sortDirection)
                             ->orderBy('start_time', 'asc');
            
            if ($this->type && in_array($this->type, ['external', 'internal'])) {
                $query->where('type', $this->type);
            }
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('location', 'like', "%{$this->search}%");
                });
            }
            
            // We can only select what's visible or all?
            // User requested "select", usually implies visible.
            // But doing a full query `pluck` selects ALL matching results, not just page.
            // Let's select ALL matching results for convenience, or just the ones visible?
            // Standard grid usually selects current page. 
            // But we don't have pagination enabled in the query above (it uses ->get()).
            // Wait, usages of `use WithPagination` is present but `render` uses `->get()`.
            // So there is NO pagination currently implemented in render! It gets ALL.
            // So `->pluck('id')` is safe and correct.
            
            $this->selected = $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) return;

        // Retrieve models to trigger 'deleting' event for file cleanup
        $activities = Activity::whereIn('id', $this->selected)->get();
        // Store IDs for potential Undo
        $this->deletedIds = $this->selected;
        
        foreach ($activities as $activity) {
            $activity->delete(); // Soft Delete
        }

        $this->selected = [];
        $this->selectAll = false;
        
        // No flash message here, handled by frontend toast
    }

    public function delete($id)
    {
        $activity = Activity::find($id);
        
        if ($activity) {
            // Check authorization if needed, currently reliant on view visibility or add check here
            // if (auth()->user()->cannot('delete', $activity)) abort(403);
            
            $this->deletedIds = [$id];
            $activity->delete(); // Soft Delete
            // No flash message here, handled by frontend toast
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
            $activities = Activity::withTrashed()->whereIn('id', $this->deletedIds)->get();
            foreach ($activities as $activity) {
                $activity->forceDelete();
            }
            $this->deletedIds = [];
            $this->dispatch('alert', type: 'success', message: 'Kegiatan berhasil dihapus permanen.');
        }
    }

    public function render()
    {
        $query = Activity::query();
        
        // Apply Visibility Scope
        if (auth()->check()) {
            // $query->visibleToUser(auth()->user()); // Removed per user request: lists show all
        }

        $query->where('start_date', '>=', now()->startOfDay())
              ->orderBy('start_date', $this->sortDirection)
              ->orderBy('start_time', 'asc');

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

        // Use pagination instead of getting all records
        $activities = $query->paginate(15);

        // Group the records on the current page
        $groupedActivities = $activities->getCollection()->groupBy(function($item) {
            return $item->start_date->isoFormat('MMMM Y');
        });

        // Pass both the paginated object (for links) and the grouped collection (for display)
        return view('livewire.activity-list', compact('groupedActivities', 'activities'));
    }
}
