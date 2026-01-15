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

            // Delete Documentation Photos
            foreach ($activity->documentations as $doc) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
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
        ],
        'Sekretariat DJSN' => [
            'Imron Rosadi'
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

