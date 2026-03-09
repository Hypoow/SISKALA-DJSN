<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'letter_number',


        'name',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'pic',
        'narasumber',
        'status',
        'invitation_status',
        'invitation_type',
        'location_type',
        'location',
        'media_online',
        'meeting_link',
        'meeting_id',
        'passcode',
        'dispo_note',
        'disposition_to',
        'dresscode',
        'attachment_path',
        'minutes_path',
        'assignment_letter_path',
        'google_event_id',
        'summary_content',
        'google_event_id_dewan',
        'google_event_id_sekretariat',
        'organizer_name',
        'attendance_list',
        'updated_by',
        'attendance_details',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pic' => 'array',
        'narasumber' => 'array',
        'disposition_to' => 'array',
        'attendance_list' => 'array',
        'attendance_details' => 'array',
    ];

    /**
     * Get the user who last updated the activity.
     */
    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    public function materials()
    {
        return $this->hasMany(ActivityMaterial::class);
    }

    public function moms()
    {
        return $this->hasMany(ActivityMom::class);
    }

    protected static function booted()
    {
        // Use forceDeleting so files are only removed when permanently deleted
        static::forceDeleting(function ($activity) {
            // Delete Google Calendar Events
            \App\Services\GoogleCalendarService::deleteEvent($activity);

            // Delete Attachment (Surat Undangan)
            if ($activity->attachment_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($activity->attachment_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($activity->attachment_path);
            }

            // Delete Minutes (Notulensi)
            if ($activity->minutes_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($activity->minutes_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($activity->minutes_path);
            }

            // Delete Assignment Letter (Surat Tugas)
            if ($activity->assignment_letter_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($activity->assignment_letter_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($activity->assignment_letter_path);
            }
            
            // Delete Maintainance Files
            foreach ($activity->materials as $material) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($material->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($material->file_path);
                }
            }

            // Delete MoMs
            foreach ($activity->moms as $mom) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($mom->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($mom->file_path);
                }
            }

            // Delete Documentation Photos
            foreach ($activity->documentations as $doc) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
                }
            }

            // Delete MoMs
            foreach ($activity->moms as $mom) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($mom->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($mom->file_path);
                }
            }
        });
    }

    /**
     * Get the documentations for the activity.
     */
    public function documentations()
    {
        return $this->hasMany(ActivityDocumentation::class);
    }

    /**
     * Virtual accessor for date_time to maintain backward compatibility with views.
     */
    public function getDateTimeAttribute()
    {
        if (!$this->start_date) {
            return null;
        }
        return \Carbon\Carbon::parse($this->start_date->format('Y-m-d') . ' ' . $this->start_time);
    }
    
    public const COUNCIL_STRUCTURE = [
        'Ketua DJSN' => [
            'Nunung Nuryartono'
        ],
        'Komisi PME' => [
            'Muttaqien',
            'Nikodemus Beriman Purba',
            'Sudarto',
            'Robben Rico',
            'Mahesa Paranadipa Maykel',
            'Syamsul Hidayat Pasaribu',
            'Hermansyah'
        ],
        'Komjakum' => [
            'Paulus Agung Pambudhi',
            'Agus Taufiqurrohman',
            'Kunta Wibawa Dasa Nugraha',
            'Indah Anggoro Putri',
            'Rudi Purwono',
            'Mickael Bobby Hoelman',
            'Royanto Purba'
        ],
        'Sekretariat DJSN' => [
            'Imron Rosadi'
        ]
    ];

    /**
     * Get disposition groups based on selected names
     */
    public function getDispositionGroupsAttribute()
    {
        $selectedNames = $this->disposition_to ?? [];
        if (empty($selectedNames)) return [];

        $groups = [];
        
        foreach (self::COUNCIL_STRUCTURE as $groupName => $members) {
            // Check if ANY member of this group is in the selected names
            // Or should it be ALL? Usually "Disposisi to Komisi X" implies at least one member.
            // Let's check intersection.
            if (count(array_intersect($members, $selectedNames)) > 0) {
                $groups[] = $groupName;
            }
        }

        // If no group found (maybe manual names), return them or a generic label?
        // For now, let's just return the groups found. 
        // If "Sekretariat" is selected, it might just be individual staff, but usually they have a group.
        
        return array_unique($groups);
    }

    /**
     * Get list of members in a specific group that are in the disposition list.
     * Used for tooltips.
     */
    public function getDispositionGroupMembers($groupName)
    {
        $allSelected = $this->disposition_to ?? [];
        if (empty($allSelected) || !isset(self::COUNCIL_STRUCTURE[$groupName])) {
            return ''; // No members or invalid group
        }

        $groupMembers = self::COUNCIL_STRUCTURE[$groupName];
        $foundMembers = array_intersect($groupMembers, $allSelected);

        if (empty($foundMembers)) return '';

        if ($groupName === 'Ketua DJSN' || count($foundMembers) === 1) {
            return reset($foundMembers);
        }

        // Fetch user records to get the dynamic order based on master users
        $orderedUsers = \App\Models\User::whereIn('name', $foundMembers)
            ->orderBy('order', 'asc')
            ->pluck('name')
            ->toArray();

        // Include any names that might not be in the users table just in case
        $missing = array_diff($foundMembers, $orderedUsers);
        $finalMembers = array_merge($orderedUsers, $missing);

        $html = "<div class='text-left pl-1'>";
        foreach ($finalMembers as $member) {
            $html .= "&bull; " . $member . "<br>";
        }
        $html .= "</div>";

        return $html;
    }

    // Internal PIC Options
    public const INTERNAL_PICS = [
        'Ketua DJSN',
        'Komisi PME',
        'Komjakum',
        'Sekretariat DJSN'
    ];

    // Constants for Status
    public const STATUS_ON_SCHEDULE = 0;
    public const STATUS_RESCHEDULE = 1;
    public const STATUS_NO_DISPO = 2;
    public const STATUS_CANCELLED = 3;

    // Constants for Invitation Status (External)
    public const INV_EXT_PROCESS = 0;
    public const INV_EXT_DISPO = 1;
    public const INV_EXT_INFO = 2;
    public const INV_EXT_ATTEND = 3;

    // Constants for Invitation Status (Internal)
    public const INV_INT_SENT = 0;
    public const INV_INT_SIGNED = 1;
    public const INV_INT_DRAFT = 2;

    /**
     * Scope a query to only include activities visible to the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisibleToUser($query, $user)
    {
        if ($user->isAdmin()) {
            return $query; // Admin sees all
        }

        if (in_array($user->role, ['Dewan', 'DJSN', 'TA', 'User'])) {
            // Dewan/DJSN/TA/User see if:
            // 1. They are explicitly in disposition_to (Legacy or specific assignment)
            // 2. They are the PIC
            // 3. A Dewan member from their Commission/Division is in disposition_to (New Requirement)
            
            return $query->where(function($q) use ($user) {
                // 1. Direct Disposition
                $q->whereJsonContains('disposition_to', $user->name)
                  // 2. Direct PIC
                  ->orWhereJsonContains('pic', $user->name);
                  
                // 3. Commission Match for TA/User/Dewan
                // Identify User's Commission based on Divisi
                $userDivisi = strtoupper($user->divisi ?? '');
                $commissionMembers = [];

                if (str_contains($userDivisi, 'PME')) {
                    $commissionMembers = self::COUNCIL_STRUCTURE['Komisi PME'] ?? [];
                } elseif (str_contains($userDivisi, 'KOMJAKUM')) {
                    $commissionMembers = self::COUNCIL_STRUCTURE['Komjakum'] ?? [];
                } elseif ($userDivisi == 'KETUA DJSN' || str_contains($userDivisi, 'KETUA')) {
                     $commissionMembers = self::COUNCIL_STRUCTURE['Ketua DJSN'] ?? [];
                }
                
                // If user belongs to a known commission, check if ANY of that commission's members are in disposition_to
                if (!empty($commissionMembers)) {
                    foreach ($commissionMembers as $member) {
                        $q->orWhereJsonContains('disposition_to', $member);
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Get the follow-ups for the activity.
     */
    public function followups()
    {
        return $this->hasMany(ActivityFollowup::class);
    }

    /**
     * Prune soft deleted activities older than specified minutes.
     * 
     * @param int $minutes
     * @return int Number of deleted records
     */
    public static function pruneTrash($minutes = 60, $limit = 5)
    {
        $cutoff = now()->subMinutes($minutes);
        
        // Find trashed items older than cutoff
        $trashed = static::onlyTrashed()
                         ->where('deleted_at', '<', $cutoff)
                         ->limit($limit)
                         ->get();
                         
        $count = 0;
        foreach ($trashed as $activity) {
            $activity->forceDelete();
            $count++;
        }
        
        return $count;
    }
}

