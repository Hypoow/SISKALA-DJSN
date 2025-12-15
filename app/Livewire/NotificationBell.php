<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public function getNotificationsProperty()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        // Admin: Urgent Activities (H-3, No Dispo)
        if ($user->isAdmin()) {
            return Activity::whereBetween('start_date', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
                           ->where('status', '!=', Activity::STATUS_CANCELLED) 
                           ->where(function($q) {
                               $q->whereNull('disposition_to')
                                 ->orWhere('disposition_to', '[]');
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
                           ->orderBy('start_date', 'asc')
                           ->get();
        }

        return collect();
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => $this->notifications
        ]);
    }
}
