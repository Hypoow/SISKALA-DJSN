<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination;

    public $perPage = 10;

    public function updatingPerPage()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function getPicColor($picName)
    {
        // Define specific colors for known Commissions
        $map = [
            'Komjakum' => 'komjakum',
            'Komisi PME' => 'pme',
            'PME' => 'pme',
            'Sekretariat DJSN' => 'sekretariat',
            'Sekretaris DJSN' => 'sekretariat',
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
        // Auto-cleanup trash (force delete soft-deleted items after 60 mins, limit 5 to avoid slow loading)
        Activity::pruneTrash(60, 5);
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
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    public function updatingSortDirection()
    {
        $this->resetSelectionState();
        $this->resetPage();
    }

    // Bulk Delete Logic
    public $selected = [];
    public $selectAll = false;
    public $deletedIds = []; // Track deleted IDs for Undo

    public function updatingPaginators($page, $pageName)
    {
        $this->resetSelectionState();
    }

    public function updatedSelectAll($value)
    {
        abort_unless(auth()->user()->canManageActivities(), 403);

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

    public function deleteSelected()
    {
        abort_unless(auth()->user()->canManageActivities(), 403);

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
        abort_unless(auth()->user()->canManageActivities(), 403);

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
        abort_unless(auth()->user()->canManageActivities(), 403);

        if (!empty($this->deletedIds)) {
            Activity::withTrashed()->whereIn('id', $this->deletedIds)->restore();
            $this->deletedIds = [];
            $this->dispatch('alert', type: 'success', message: 'Penghapusan dibatalkan.');
        }
    }

    public function forceDeleteDeleted()
    {
        abort_unless(auth()->user()->canManageActivities(), 403);

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
        $activities = $this->getFilteredActivitiesQuery()->paginate($this->perPage);

        // Group the records on the current page
        $groupedActivities = $activities->getCollection()->groupBy(function($item) {
            return $item->start_date->isoFormat('MMMM Y');
        });

        // Pass both the paginated object (for links) and the grouped collection (for display)
        return view('livewire.activity-list', compact('groupedActivities', 'activities'));
    }

    private function getFilteredActivitiesQuery(): Builder
    {
        $query = Activity::query();

        if (auth()->check()) {
            $query->visibleToUser(auth()->user());
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
}
