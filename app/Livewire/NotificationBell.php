<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use App\Models\ActivityFollowup;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public function clearHistory()
    {
        $hiddenActivities = session()->get('hidden_activity_ids', []);
        $hiddenFollowups = session()->get('hidden_followup_ids', []);

        foreach ($this->notifications as $notification) {
            if ($notification->type === 'followup') {
                $hiddenFollowups[] = $notification->id;
            } else {
                $hiddenActivities[] = $notification->id;
            }
        }
        
        session()->put('hidden_activity_ids', array_values(array_unique($hiddenActivities)));
        session()->put('hidden_followup_ids', array_values(array_unique($hiddenFollowups)));
        
        $this->dispatch('show-toast', type: 'success', message: 'Riwayat notifikasi dihapus.');
    }

    public function markAllAsRead()
    {
        $readNotifications = session()->get('read_notifications', []);
        $notifications = $this->notifications; // Access property

        foreach ($notifications as $notification) {
             $key = ($notification->type === 'followup' ? 'followup-' : 'activity-') . $notification->id;
             if (!in_array($key, $readNotifications)) {
                 $readNotifications[] = $key;
             }
        }
        
        session()->put('read_notifications', $readNotifications);
        
        $this->dispatch('show-toast', type: 'success', message: 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function markAsRead($id, $url)
    {
        $readNotifications = session()->get('read_notifications', []);
        
        if (!in_array($id, $readNotifications)) {
            $readNotifications[] = $id;
            session()->put('read_notifications', $readNotifications);
        }
        
        return redirect()->to($url);
    }

    public function getNotificationsProperty()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $hiddenActivities = session()->get('hidden_activity_ids', []);
        $hiddenFollowups = session()->get('hidden_followup_ids', []);

        $notifications = collect();

        // Activity managers: urgent activities without disposition.
        if ($user->canManageActivities()) {
            $activities = Activity::whereBetween('start_date', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
                           ->where('status', '!=', Activity::STATUS_CANCELLED) 
                           ->where(function($q) {
                               $q->whereNull('disposition_to')
                                 ->orWhere('disposition_to', '[]');
                           })
                           ->whereNotIn('id', $hiddenActivities)
                           ->orderBy('start_date', 'asc')
                           ->get();
            $notifications = $notifications->merge($activities);
        }

        if ($user->isTA() || $user->isPersidangan()) {
            $activities = Activity::visibleToUser($user)
                ->where('start_date', '>=', now()->startOfDay())
                ->where('status', '!=', Activity::STATUS_CANCELLED)
                ->whereNotIn('id', $hiddenActivities)
                ->orderBy('start_date', 'asc')
                ->get();
            $notifications = $notifications->merge($activities);
        }

        if ($user->canReceiveDisposition() && !$user->isTA() && !$user->isPersidangan()) {
            $activities = Activity::where('start_date', '>=', now()->startOfDay())
                ->where('status', '!=', Activity::STATUS_CANCELLED)
                ->whereJsonContains('disposition_to', $user->name)
                ->whereNotIn('id', $hiddenActivities)
                ->orderBy('start_date', 'asc')
                ->get();
            $notifications = $notifications->merge($activities);
        }

        if ($user->canManageFollowUp() || $user->isDewan() || $user->isTA() || $user->isPersidangan()) {
            $followUps = ActivityFollowup::with('activity')
                ->whereBetween('deadline', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
                ->whereIn('status', [0, 1])
                ->whereNotIn('id', $hiddenFollowups)
                ->whereHas('activity', function ($query) use ($user) {
                    $query->visibleToUser($user);
                })
                ->get()
                ->map(function($item) {
                    $item->type = 'followup';
                    return $item;
                });

            $notifications = $notifications->merge($followUps);
        }

        return $notifications->sortBy(function($item) {
            return $item->type === 'followup' ? $item->deadline : $item->start_date;
        });
    }

    public $knownNotificationIds = [];

    public function mount()
    {
        // Initialize with current IDs to avoid alerting on existing notifications during page load
        $this->knownNotificationIds = $this->getNotificationsProperty()->map(function($n) {
             return ($n->type === 'followup' ? 'followup-' : 'activity-') . $n->id;
        })->toArray();
    }

    public function render()
    {
        $currentNotifications = $this->getNotificationsProperty();
        
        // Check for new notifications
        $currentIds = $currentNotifications->map(function($n) {
             return ($n->type === 'followup' ? 'followup-' : 'activity-') . $n->id;
        })->toArray();

        $newIds = array_diff($currentIds, $this->knownNotificationIds);

        // Alert for new notifications
        if (!empty($newIds)) {
            foreach ($newIds as $newId) {
                // Find the notification object to get details
                $notification = $currentNotifications->first(function($n) use ($newId) {
                    return (($n->type === 'followup' ? 'followup-' : 'activity-') . $n->id) === $newId;
                });

                if ($notification) {
                    $title = $notification->type === 'followup' ? $notification->instruction : $notification->name;
                    $typeTitle = $notification->type === 'followup' ? 'Tindak Lanjut Baru' : 'Kegiatan Baru';
                    
                    $this->dispatch('alert', 
                        type: 'info',
                        message: "{$typeTitle}: {$title}"
                    );
                }
            }
            
            // Update known IDs
            $this->knownNotificationIds = $currentIds;
        }

        $readNotifications = session()->get('read_notifications', []);
        $unreadCount = $currentNotifications->filter(function($n) use ($readNotifications) {
            $key = ($n->type === 'followup' ? 'followup-' : 'activity-') . $n->id;
            return !in_array($key, $readNotifications);
        })->count();

        return view('livewire.notification-bell', [
            'notifications' => $currentNotifications,
            'readNotifications' => $readNotifications,
            'unreadCount' => $unreadCount
        ]);
    }
}
