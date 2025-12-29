<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityFollowup;
use App\Models\Activity;
use Carbon\Carbon;

class FollowUpDashboard extends Component
{
    use WithPagination;

    // Filters
    public $year;
    public $topic;
    public $status = 'all';
    public $search;

    // UI Logic
    public $editingProgressId = null;
    public $progressNote = '';
    public $percentage = 0;

    public function mount()
    {
        $this->year = Carbon::now()->year;
    }

    public function getListeners()
    {
        return ['followup-updated' => '$refresh'];
    }

    public function editProgress($id)
    {
        $this->editingProgressId = $id;
        $item = ActivityFollowup::find($id);
        $this->progressNote = $item->progress_notes;
        $this->percentage = $item->percentage;
    }

    public function saveProgress($id)
    {
        $item = ActivityFollowup::find($id);
        $item->update([
            'progress_notes' => $this->progressNote,
            'percentage' => $this->percentage
        ]);
        $this->editingProgressId = null;
    }

    public function updateStatus($id, $status)
    {
        $item = ActivityFollowup::find($id);
        $item->update(['status' => $status]);
    }

    public function render()
    {
        $query = Activity::query()
            ->with(['followups' => function($q) {
                $q->orderBy('status', 'asc');
            }])
            ->whereHas('followups', function($q) {
                if ($this->status !== 'all') {
                    $q->where('status', $this->status);
                }
                if ($this->search) {
                     $q->where('instruction', 'like', '%' . $this->search . '%')
                       ->orWhere('pic', 'like', '%' . $this->search . '%');
                }
            });

        // Date Filter on Activity
        if ($this->year) {
             $query->whereYear('start_date', $this->year);
        }
              
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('followups', function($sq) {
                        $sq->where('instruction', 'like', '%' . $this->search . '%')
                           ->orWhere('pic', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Order by Newest First (Activity Date)
        $activities = $query->orderBy('start_date', 'desc')->paginate(50);
        
        // Group by Month-Year
        $groupedActivities = $activities->getCollection()->groupBy(function($date) {
            return Carbon::parse($date->start_date)->translatedFormat('F Y'); // Translates to Indonesian if locale set
        });

        // Stats Calculation (Filtered by Year)
        $baseStatsQuery = ActivityFollowup::whereHas('activity', function($q) {
             if($this->year) {
                $q->whereYear('start_date', $this->year);
             }
        });

        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'completed' => (clone $baseStatsQuery)->where('status', 2)->count(),
            'pending' => (clone $baseStatsQuery)->whereIn('status', [0, 1])->count(),
        ];
        
        $statusLabels = [
            0 => 'Pending',
            1 => 'On Progress',
            2 => 'Selesai',
            3 => 'Batal'
        ];
         $statusColors = [
            0 => 'secondary',
            1 => 'warning',
            2 => 'success',
            3 => 'danger'
        ];

        return view('livewire.follow-up-dashboard', [
            'activities' => $activities, // Pass paginator for links
            'groupedActivities' => $groupedActivities, // Pass grouped collection for loop
            'stats' => $stats,
            'statusLabels' => $statusLabels,
            'statusColors' => $statusColors,
        ]);
    }
}
