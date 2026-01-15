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
    public $month = '';
    public $topic;
    public $status = 'all';
    public $search;

    // UI Logic
    public $editingProgressId = null;
    public $progressNote = '';

    public $existingTopics = [];

    public function mount()
    {
        $this->year = Carbon::now()->year;
        $this->month = Carbon::now()->month;
        $this->existingTopics = ActivityFollowup::distinct()->whereNotNull('topic')->pluck('topic')->toArray();
    }

    public function updatedYear() { $this->resetPage(); }
    public function updatedMonth() { $this->resetPage(); }
    public function updatedTopic() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }
    public function updatedSearch() { $this->resetPage(); }

    public function getListeners()
    {
        return ['followup-updated' => '$refresh'];
    }

    public function getPicColor($picName)
    {
        // Define specific colors for known Commissions
        $map = [
            'Komjakum' => 'komjakum', // Blue custom class
            'Komisi PME' => 'pme', // Green custom class
            'PME' => 'pme',
            'Sekretariat DJSN' => 'sekretariat', // Orange custom class
            'Anggota DJSN' => 'djsn',
            'Ketua DJSN' => 'ketua', // Purple custom class
        ];

        if (str_contains(strtoupper($picName), 'PME')) {
            return 'pme';
        }

        if (isset($map[$picName])) {
            return $map[$picName];
        }

        // Return a default or hash-based color if not found
        $colors = ['primary', 'secondary', 'info', 'dark', 'warning'];
        $index = crc32($picName) % count($colors);
        return $colors[$index];
    }
    
    // Helper to get Topic Color directly from DB
    public function getTopicColor($topicName)
    {
        $topic = \App\Models\Topic::where('name', $topicName)->first();
        if ($topic) {
            return $topic->color;
        }
        return '#007bff'; // Default blue
    }

    public function editProgress($id)
    {
        $this->editingProgressId = $id;
        $item = ActivityFollowup::find($id);
        $this->progressNote = $item->progress_notes;
    }

    public function saveProgress($id)
    {
        $item = ActivityFollowup::find($id);
        $item->update([
            'progress_notes' => $this->progressNote
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
        // Refresh topics list on render to get new ones
        $this->existingTopics = ActivityFollowup::distinct()->whereNotNull('topic')->pluck('topic')->toArray();

        $query = Activity::query()
            ->with(['followups' => function($q) {
                $q->orderBy('pic', 'asc')
                  ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC')
                  ->orderBy('id', 'asc');
            }])
            ->whereHas('followups', function($q) {
                if ($this->status !== 'all') {
                    $q->where('status', $this->status);
                }
                if ($this->topic) {
                     $q->where('topic', $this->topic);
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
        if ($this->month) {
             $query->whereMonth('start_date', $this->month);
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

        // Stats Calculation (Filtered by Year AND Month)
        $baseStatsQuery = ActivityFollowup::whereHas('activity', function($q) {
             if($this->year) {
                $q->whereYear('start_date', $this->year);
             }
             if($this->month) {
                $q->whereMonth('start_date', $this->month);
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
