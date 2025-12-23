<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public function clearHistory()
    {
        // Get all current notification IDs
        $currentIds = $this->notifications->pluck('id')->toArray();
        $hiddenNotifications = session()->get('hidden_notifications', []);
        
        // Merge and save
        $newHidden = array_unique(array_merge($hiddenNotifications, $currentIds));
        session()->put('hidden_notifications', $newHidden);
    }

    public function markAsRead($activityId, $url)
    {
        $readNotifications = session()->get('read_notifications', []);
        if (!in_array($activityId, $readNotifications)) {
            $readNotifications[] = $activityId;
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

        $hiddenNotifications = session()->get('hidden_notifications', []);

        // Admin: Urgent Activities (H-3, No Dispo)
        if ($user->isAdmin()) {
            return Activity::whereBetween('start_date', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
                           ->where('status', '!=', Activity::STATUS_CANCELLED) 
                           ->where(function($q) {
                               $q->whereNull('disposition_to')
                                 ->orWhere('disposition_to', '[]');
                           })
                           ->where(function($q) use ($hiddenNotifications) {
                               $q->whereNotIn('id', $hiddenNotifications);
                           })
                           ->orderBy('start_date', 'asc')
                           ->get();
        }
        
        // Dewan/DJSN: Activities assigned to them (upcoming only)
        if (in_array($user->role, ['Dewan', 'DJSN'])) {
            return Activity::where('start_date', '>=', now()->startOfDay())
                           ->where('status', '!=', Activity::STATUS_CANCELLED)
                           ->where(function($q) use ($user) {
                               $q->whereJsonContains('disposition_to', $user->name);
                           })
                           ->where(function($q) use ($hiddenNotifications) {
                               $q->whereNotIn('id', $hiddenNotifications);
                           })
                           ->orderBy('start_date', 'asc')
                           ->get();
        }

        return collect();
    }

    public function render()
    {
        $readNotifications = session()->get('read_notifications', []);
        $unreadCount = $this->notifications->whereNotIn('id', $readNotifications)->count();

        return view('livewire.notification-bell', [
            'notifications' => $this->notifications,
            'readNotifications' => $readNotifications,
            'unreadCount' => $unreadCount
        ]);
    }
}
