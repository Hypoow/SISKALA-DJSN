<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';

    public $sortDirection = 'asc';

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

        Activity::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->selectAll = false;
        
        session()->flash('message', 'Kegiatan terpilih berhasil dihapus.');
    }

    public function render()
    {
        $query = Activity::where('start_date', '>=', now()->startOfDay())
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

        $activities = $query->get();

        $groupedActivities = $activities->groupBy(function($item) {
            return $item->start_date->isoFormat('MMMM Y');
        });

        return view('livewire.activity-list', compact('groupedActivities'));
    }
}
