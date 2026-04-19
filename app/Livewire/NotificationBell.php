<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use App\Models\ActivityFollowup;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $knownNotificationIds = [];

    protected function notificationKey($notification): string
    {
        if (($notification->type ?? null) === 'followup') {
            $stage = $notification->notification_stage ?? 'general';

            return 'followup-' . $notification->id . '-' . $stage;
        }

        return 'activity-' . $notification->id;
    }

    protected function hiddenNotificationKeys(): array
    {
        $currentKeys = session()->get('hidden_notification_keys', []);
        $legacyActivityKeys = collect(session()->get('hidden_activity_ids', []))
            ->map(fn ($id) => 'activity-' . $id)
            ->all();

        return array_values(array_unique(array_merge($currentKeys, $legacyActivityKeys)));
    }

    protected function followupNotificationStage(ActivityFollowup $followup): ?string
    {
        if (!$followup->deadline || !in_array($followup->status, [
            ActivityFollowup::STATUS_PENDING,
            ActivityFollowup::STATUS_ON_PROGRESS,
        ], true)) {
            return null;
        }

        $daysRemaining = now()->startOfDay()->diffInDays($followup->deadline->copy()->startOfDay(), false);

        if ($daysRemaining < 0) {
            return null;
        }

        if ($daysRemaining <= 3) {
            return 'h3';
        }

        if ($daysRemaining <= 7) {
            return 'h7';
        }

        return null;
    }

    protected function decorateFollowupNotification(ActivityFollowup $followup): ?ActivityFollowup
    {
        $stage = $this->followupNotificationStage($followup);

        if ($stage === null) {
            return null;
        }

        $daysRemaining = now()->startOfDay()->diffInDays($followup->deadline->copy()->startOfDay(), false);

        $followup->type = 'followup';
        $followup->notification_stage = $stage;
        $followup->notification_stage_title = $stage === 'h3' ? 'H-3' : 'H-7';
        $followup->notification_days_remaining = $daysRemaining;
        $followup->notification_label = $stage === 'h3' ? 'Deadline H-3!' : 'Deadline H-7!';
        $followup->notification_badge_class = $stage === 'h3' ? 'badge-danger' : 'badge-warning text-white';
        $followup->notification_text_class = $stage === 'h3' ? 'text-danger' : 'text-warning';
        $followup->notification_icon_bg = $stage === 'h3'
            ? 'bg-danger-light text-danger'
            : 'bg-warning-light text-warning';

        return $followup;
    }

    public function clearHistory()
    {
        $hiddenNotificationKeys = $this->hiddenNotificationKeys();

        foreach ($this->notifications as $notification) {
            $key = $this->notificationKey($notification);

            if (!in_array($key, $hiddenNotificationKeys, true)) {
                $hiddenNotificationKeys[] = $key;
            }
        }

        session()->put('hidden_notification_keys', array_values(array_unique($hiddenNotificationKeys)));
        session()->forget(['hidden_activity_ids', 'hidden_followup_ids']);

        $this->dispatch('show-toast', type: 'success', message: 'Riwayat notifikasi dihapus.');
    }

    public function markAllAsRead()
    {
        $readNotifications = session()->get('read_notifications', []);
        $notifications = $this->notifications; // Access property

        foreach ($notifications as $notification) {
            $key = $this->notificationKey($notification);

            if (!in_array($key, $readNotifications, true)) {
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

        $hiddenNotificationKeys = $this->hiddenNotificationKeys();

        $notifications = collect();

        // Activity managers: urgent activities without disposition.
        if ($user->canManageActivities()) {
            $activities = Activity::whereBetween('start_date', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
                           ->where('status', '!=', Activity::STATUS_CANCELLED) 
                           ->where(function($q) {
                               $q->whereNull('disposition_to')
                                 ->orWhere('disposition_to', '[]');
                           })
                           ->orderBy('start_date', 'asc')
                           ->get()
                           ->reject(fn ($item) => in_array($this->notificationKey($item), $hiddenNotificationKeys, true));
            $notifications = $notifications->merge($activities);
        }

        if ($user->isTA() || $user->isPersidangan()) {
            $activities = Activity::visibleToUser($user)
                ->where('start_date', '>=', now()->startOfDay())
                ->where('status', '!=', Activity::STATUS_CANCELLED)
                ->orderBy('start_date', 'asc')
                ->get()
                ->reject(fn ($item) => in_array($this->notificationKey($item), $hiddenNotificationKeys, true));
            $notifications = $notifications->merge($activities);
        }

        if ($user->canReceiveDisposition() && !$user->isTA() && !$user->isPersidangan()) {
            $activities = Activity::where('start_date', '>=', now()->startOfDay())
                ->where('status', '!=', Activity::STATUS_CANCELLED)
                ->whereJsonContains('disposition_to', $user->name)
                ->orderBy('start_date', 'asc')
                ->get()
                ->reject(fn ($item) => in_array($this->notificationKey($item), $hiddenNotificationKeys, true));
            $notifications = $notifications->merge($activities);
        }

        if ($user->canManageFollowUp() || $user->isDewan() || $user->isTA() || $user->isPersidangan()) {
            $followUps = ActivityFollowup::with('activity')
                ->whereBetween('deadline', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
                ->whereIn('status', [
                    ActivityFollowup::STATUS_PENDING,
                    ActivityFollowup::STATUS_ON_PROGRESS,
                ])
                ->whereHas('activity', function ($query) use ($user) {
                    $query->visibleToUser($user);
                })
                ->orderBy('deadline', 'asc')
                ->get()
                ->map(fn (ActivityFollowup $item) => $this->decorateFollowupNotification($item))
                ->filter()
                ->reject(fn ($item) => in_array($this->notificationKey($item), $hiddenNotificationKeys, true))
                ->values();

            $notifications = $notifications->merge($followUps);
        }

        return $notifications->sortBy(function($item) {
            return $item->type === 'followup' ? $item->deadline : $item->start_date;
        })->values();
    }

    public function mount()
    {
        // Initialize with current IDs to avoid alerting on existing notifications during page load
        $this->knownNotificationIds = $this->getNotificationsProperty()->map(function($n) {
             return $this->notificationKey($n);
        })->toArray();
    }

    public function render()
    {
        $currentNotifications = $this->getNotificationsProperty();
        
        // Check for new notifications
        $currentIds = $currentNotifications->map(function($n) {
             return $this->notificationKey($n);
        })->toArray();

        $newIds = array_diff($currentIds, $this->knownNotificationIds);

        // Alert for new notifications
        if (!empty($newIds)) {
            foreach ($newIds as $newId) {
                // Find the notification object to get details
                $notification = $currentNotifications->first(function($n) use ($newId) {
                    return $this->notificationKey($n) === $newId;
                });

                if ($notification) {
                    $title = $notification->type === 'followup' ? $notification->instruction : $notification->name;
                    $typeTitle = $notification->type === 'followup'
                        ? 'Pengingat Tindak Lanjut ' . ($notification->notification_stage_title ?? '')
                        : 'Kegiatan Baru';

                    $this->dispatch('alert', 
                        type: $notification->type === 'followup' && ($notification->notification_stage ?? null) === 'h3'
                            ? 'warning'
                            : 'info',
                        message: "{$typeTitle}: {$title}"
                    );
                }
            }
            
            // Update known IDs
            $this->knownNotificationIds = $currentIds;
        }

        $readNotifications = session()->get('read_notifications', []);
        $unreadCount = $currentNotifications->filter(function($n) use ($readNotifications) {
            $key = $this->notificationKey($n);
            return !in_array($key, $readNotifications, true);
        })->count();

        return view('livewire.notification-bell', [
            'notifications' => $currentNotifications,
            'readNotifications' => $readNotifications,
            'unreadCount' => $unreadCount
        ]);
    }
}
