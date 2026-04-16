<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        'report_target_override',
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
    
    /**
     * Get the council structure dynamically from the database.
     * This replaces the hardcoded COUNCIL_STRUCTURE constant.
     */
    public static function getCouncilStructure()
    {
        $dewan = User::with(['division', 'position'])
            ->orderBy('order', 'asc')
            ->get();
        $dewan = $dewan->filter(fn (User $user) => $user->isDewan());

        $structure = [
            'Ketua DJSN' => [],
            'Komisi' => [],
            'Set.DJSN' => []
        ];

        foreach ($dewan as $user) {
            $category = $user->division->category ?? 'Komisi';
            $divName = $user->division->name ?? 'Lainnya';
            $structure[$category][$divName][] = $user->name;
        }

        // Remove empty top-level categories
        return array_filter($structure, function($divs) {
            return !empty($divs);
        });
    }

    /**
     * Get disposition groups based on selected names
     */
    public function getDispositionGroupsAttribute()
    {
        return self::derivePicGroupsFromDispositionNames($this->disposition_to ?? []);
    }

    /**
     * Get list of members in a specific group that are in the disposition list.
     * Used for tooltips.
     */
    public function getDispositionGroupMembers($groupName)
    {
        $allSelected = $this->disposition_to ?? [];
        if (empty($allSelected)) {
            return '';
        }

        $selectedUsers = User::with(['division', 'position'])
            ->whereIn('name', $allSelected)
            ->orderBy('order', 'asc')
            ->get()
            ->keyBy('name');

        $finalMembers = [];
        foreach ($allSelected as $name) {
            $user = $selectedUsers->get($name);
            $resolvedGroup = self::resolveInternalPicGroupForUser($user)
                ?? self::normalizeInternalPicLabel($name);

            if ($resolvedGroup === $groupName) {
                $finalMembers[] = $name;
            }
        }

        $finalMembers = array_values(array_unique($finalMembers));
        if (empty($finalMembers)) {
            return '';
        }

        if ($groupName === 'Ketua DJSN' || count($finalMembers) === 1) {
            return reset($finalMembers);
        }

        $html = "<div class='text-left pl-1'>";
        foreach ($finalMembers as $member) {
            $html .= "&bull; " . $member . "<br>";
        }
        $html .= "</div>";

        return $html;
    }

    public function getDisplayPicGroupsAttribute()
    {
        if ($this->type === 'external') {
            return array_values(array_filter(is_array($this->pic) ? $this->pic : [$this->pic]));
        }

        $groups = $this->disposition_groups;
        if (!empty($groups)) {
            return $groups;
        }

        $fallback = [];
        foreach ((array) ($this->pic ?? []) as $pic) {
            $normalized = self::normalizeInternalPicLabel($pic);
            if ($normalized) {
                $fallback[] = $normalized;
            }
        }

        return self::sortInternalPicGroups($fallback);
    }

    public function getGroupedDispositionUsersAttribute()
    {
        $selectedNames = $this->disposition_to ?? [];
        if (empty($selectedNames)) {
            return collect();
        }

        $selectedUsers = User::with(['division', 'position'])
            ->whereIn('name', $selectedNames)
            ->orderBy('order', 'asc')
            ->get();

        $grouped = collect();
        foreach ($selectedUsers as $user) {
            $group = trim((string) $user->disposition_group_label);

            if ($group === '') {
                $group = self::resolveInternalPicGroupForUser($user) ?? 'Sekretariat DJSN';
            }

            if (!$grouped->has($group)) {
                $grouped->put($group, collect());
            }

            $grouped->get($group)->push($user);
        }

        return $grouped->sortBy(function ($users, $groupName) {
            return self::getInternalPicPriority($groupName);
        });
    }

    public static function buildVisualizationGroupsFromDisposition(array $selectedNames, Collection $usersMap): Collection
    {
        if (empty($selectedNames)) {
            return collect();
        }

        $groups = collect();
        $sequence = 0;

        foreach ($selectedNames as $name) {
            $user = $usersMap->get($name);
            $groupMeta = self::resolveVisualizationGroupMeta(
                $user?->division?->display_name ?? $user?->division?->name ?? $user?->divisi,
                $user?->resolved_access_profile,
                $user?->division?->category,
                [
                    'display_label' => $user?->division?->display_name,
                    'is_commission' => (bool) ($user?->division?->is_commission ?? false),
                    'commission_code' => $user?->division?->commission_code,
                    'structure_group' => $user?->division?->structure_group,
                ]
            );

            $groupKey = $groupMeta['key'];

            if (!$groups->has($groupKey)) {
                $groups->put($groupKey, [
                    'key' => $groupKey,
                    'label' => $groupMeta['label'],
                    'type' => $groupMeta['type'],
                    'priority' => $groupMeta['priority'],
                    'sequence' => $sequence++,
                    'members' => [],
                ]);
            }

            $group = $groups->get($groupKey);
            if (!in_array($name, $group['members'], true)) {
                $group['members'][] = $name;
                $groups->put($groupKey, $group);
            }
        }

        return $groups
            ->sortBy(fn (array $group) => ($group['priority'] * 1000) + $group['sequence'])
            ->values();
    }

    public static function resolveVisualizationGroupMeta(
        ?string $label,
        ?string $role = null,
        ?string $category = null,
        array $context = []
    ): array
    {
        $normalizedLabel = self::normalizeDivisionDisplayName($label);
        $normalizedCategory = strtoupper(trim((string) $category));
        $structureGroup = $context['structure_group'] ?? null;

        if (
            $normalizedCategory === 'KETUA DJSN'
            || str_contains(strtoupper($normalizedLabel), 'KETUA DJSN')
        ) {
            return [
                'key' => 'ketua-djsn',
                'label' => 'Ketua DJSN',
                'type' => 'ketua',
                'priority' => 1,
            ];
        }

        if ($structureGroup === Division::STRUCTURE_GROUP_SECRETARY) {
            return [
                'key' => 'sekretaris-djsn',
                'label' => 'Sekretaris DJSN',
                'type' => 'sekretariat',
                'priority' => 2,
            ];
        }

        $commissionLabel = self::normalizeCommissionDisplayLabel($normalizedLabel, $role, $category, $context);
        if ($commissionLabel !== null) {
            return [
                'key' => 'komisi:' . self::normalizeVisualizationGroupKey($commissionLabel),
                'label' => $commissionLabel,
                'type' => 'komisi',
                'priority' => 3,
            ];
        }

        return [
            'key' => 'sekretariat-djsn',
                'label' => 'Sekretariat DJSN',
                'type' => 'sekretariat',
                'priority' => 4,
            ];
    }

    public static function normalizeCommissionDisplayLabel(
        ?string $label,
        ?string $role = null,
        ?string $category = null,
        array $context = []
    ): ?string
    {
        $normalizedLabel = self::normalizeDivisionDisplayName($label);
        $normalizedCategory = strtoupper(trim((string) $category));
        $normalizedRole = strtoupper(trim((string) $role));
        $displayLabel = self::normalizeDivisionDisplayName($context['display_label'] ?? $normalizedLabel);

        if ($normalizedLabel === '') {
            return null;
        }

        if (($context['is_commission'] ?? false) === true) {
            return $displayLabel !== '' ? $displayLabel : null;
        }

        if (!empty($context['commission_code'])) {
            return Division::commissionLabel($context['commission_code'], $displayLabel);
        }

        if (
            $normalizedCategory !== 'KOMISI'
            && !in_array($normalizedRole, ['DEWAN', 'TA', 'TENAGA AHLI', 'TENAGA_AHLI'], true)
            && !preg_match('/\bKOMISI\b/iu', $normalizedLabel)
        ) {
            return null;
        }

        if (str_contains(strtoupper($normalizedLabel), 'KETUA DJSN')) {
            return null;
        }

        $baseLabel = preg_replace('/^(WAKIL\s+KETUA|WAKIL|KETUA|ANGGOTA)\s+/iu', '', $normalizedLabel);
        $baseLabel = self::normalizeDivisionDisplayName($baseLabel);

        if ($baseLabel === '') {
            return null;
        }

        if (preg_match('/^KOMISI\b/iu', $baseLabel)) {
            return $baseLabel;
        }

        return 'Komisi ' . $baseLabel;
    }

    private static function normalizeDivisionDisplayName(?string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', (string) $value));
    }

    private static function normalizeVisualizationGroupKey(string $label): string
    {
        return Str::slug(self::normalizeDivisionDisplayName($label));
    }

    public static function derivePicGroupsFromDispositionNames(array $selectedNames)
    {
        if (empty($selectedNames)) {
            return [];
        }

        $selectedUsers = User::with(['division', 'position'])
            ->whereIn('name', $selectedNames)
            ->orderBy('order', 'asc')
            ->get()
            ->keyBy('name');

        $groups = [];
        foreach ($selectedNames as $name) {
            $user = $selectedUsers->get($name);
            $group = self::resolveInternalPicGroupForUser($user);

            if (!$group) {
                $group = 'Sekretariat DJSN';
            }

            $groups[] = $group;
        }

        return self::sortInternalPicGroups($groups);
    }

    public static function resolveInternalPicGroupForUser(?User $user)
    {
        if (!$user) {
            return null;
        }

        return self::normalizeInternalPicLabel(
            $user->division?->display_name ?? $user->division?->name ?? $user->divisi,
            $user->role,
            $user->division?->category ?? null,
            [
                'display_label' => $user->division?->display_name,
                'is_commission' => (bool) ($user->division?->is_commission ?? false),
                'commission_code' => $user->division?->commission_code,
                'structure_group' => $user->division?->structure_group,
            ]
        );
    }

    public static function normalizeInternalPicLabel(
        ?string $label,
        ?string $role = null,
        ?string $category = null,
        array $context = []
    )
    {
        $normalizedLabel = strtoupper(trim((string) $label));
        $normalizedRole = strtoupper(trim((string) $role));
        $normalizedCategory = strtoupper(trim((string) $category));
        $displayLabel = self::normalizeDivisionDisplayName($context['display_label'] ?? $label);

        if ($normalizedLabel === '' && $normalizedRole === '' && $normalizedCategory === '') {
            return null;
        }

        if ($normalizedLabel === 'KETUA DJSN' || $normalizedCategory === 'KETUA DJSN' || str_contains($normalizedLabel, 'KETUA DJSN')) {
            return 'Ketua DJSN';
        }

        if (($context['structure_group'] ?? null) === Division::STRUCTURE_GROUP_SECRETARY) {
            return 'Sekretaris DJSN';
        }

        if (($context['is_commission'] ?? false) === true) {
            return $displayLabel !== '' ? $displayLabel : null;
        }

        if (!empty($context['commission_code'])) {
            return Division::commissionLabel($context['commission_code'], $displayLabel);
        }

        if (str_contains($normalizedLabel, 'PME') || str_contains($normalizedLabel, 'MONITORING')) {
            return 'Komisi PME';
        }

        if (str_contains($normalizedLabel, 'KOMJAKUM') || str_contains($normalizedLabel, 'KEBIJAKAN')) {
            return 'Komjakum';
        }

        if (
            str_contains($normalizedLabel, 'SEKRETARIAT')
            || str_contains($normalizedLabel, 'SEKRETARIS')
            || str_contains($normalizedLabel, 'SET DJSN')
            || $normalizedCategory === 'SEKRETARIAT DJSN'
            || $normalizedCategory === 'SEKRETARIS DJSN'
        ) {
            return 'Sekretariat DJSN';
        }

        if (in_array($normalizedRole, ['ADMIN', 'SUPER ADMIN', 'SUPER_ADMIN', 'DJSN', 'TATA USAHA', 'PERSIDANGAN', 'BAGIAN UMUM', 'KEUANGAN', 'USER'], true)) {
            return 'Sekretariat DJSN';
        }

        return null;
    }

    public static function sortInternalPicGroups(array $groups)
    {
        $groups = array_values(array_unique(array_filter($groups)));

        usort($groups, function ($a, $b) {
            return self::getInternalPicPriority($a) <=> self::getInternalPicPriority($b);
        });

        return $groups;
    }

    public static function getInternalPicPriority(?string $groupName)
    {
        $normalized = strtoupper(trim((string) $groupName));

        $division = Division::findByDisplayLabel($groupName);
        if ($division?->is_commission) {
            return 100 + ($division->order ?? 0);
        }

        if ($division?->structure_group === Division::STRUCTURE_GROUP_SECRETARY) {
            return 50;
        }

        return match ($normalized) {
            'KETUA DJSN' => 1,
            'SEKRETARIS DJSN' => 50,
            'KOMISI PME' => 102,
            'KOMJAKUM' => 103,
            'SEKRETARIAT DJSN', 'SET DJSN', 'SET.DJSN' => 900,
            default => 99,
        };
    }

    // Internal PIC Options
    public const INTERNAL_PICS = [
        'Ketua DJSN',
        'Komisi PME',
        'Komjakum',
        'Sekretaris DJSN',
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
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->canViewAllActivities()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->whereJsonContains('disposition_to', $user->name)
                ->orWhereJsonContains('pic', $user->name);

            if ($user->isTA() || $user->isPersidangan()) {
                foreach ($user->getCommissionDewanNames() as $member) {
                    $q->orWhereJsonContains('disposition_to', $member);
                }
            }
        });
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
