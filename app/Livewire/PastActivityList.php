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

    // ... (rest of the file until render)

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
