<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'letter_number',
        'name',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'pic',
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
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pic' => 'array',
        'disposition_to' => 'array',
    ];

    /**
     * Virtual accessor for date_time to maintain backward compatibility with views.
     */
    public function getDateTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->start_date->format('Y-m-d') . ' ' . $this->start_time);
    }
    
    // Council Members List
    // Council Structure
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
        'Komisi Komjakum' => [
            'Paulus Agung Pambudhi',
            'Agus Taufiqurrohman',
            'Kunta Wibawa Dasa Nugraha',
            'Indah Anggoro Putri',
            'Rudi Purwono',
            'Mickael Bobby Hoelman',
            'Royanto Purba'
        ]
    ];

    // Internal PIC Options
    public const INTERNAL_PICS = [
        'Ketua DJSN',
        'Komisi PME',
        'Komisi Komjakum',
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

        if (in_array($user->role, ['Dewan', 'DJSN'])) {
            // Dewan/DJSN only see if they are in disposition_to OR are the PIC
            return $query->where(function($q) use ($user) {
                // Check JSON disposition_to for User Name
                $q->whereJsonContains('disposition_to', $user->name)
                  // Or checks if they are in PIC array (internal users might be listed there too)
                  ->orWhereJsonContains('pic', $user->name)
                  // Also check 'Komisi' mapping if needed? User mainly said "targeted for dispo". 
                  // Let's stick to explicit name match for now as per "disposisi pada kegiatan".
                  ;
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
}

