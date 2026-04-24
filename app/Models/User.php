<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_LEGACY_ADMIN = 'admin';
    public const ROLE_SECRETARIAT = 'DJSN';
    public const ROLE_TATA_USAHA = 'Tata Usaha';
    public const ROLE_PERSIDANGAN = 'Persidangan';
    public const ROLE_BAGIAN_UMUM = 'Bagian Umum';
    public const ROLE_KEUANGAN = 'Keuangan';
    public const ROLE_USER = 'User';
    public const ROLE_DEWAN = 'Dewan';
    public const ROLE_TA = 'TA';

    const ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_LEGACY_ADMIN, // Legacy value kept for backward compatibility
        self::ROLE_SECRETARIAT,
        self::ROLE_TATA_USAHA,
        self::ROLE_PERSIDANGAN,
        self::ROLE_BAGIAN_UMUM,
        self::ROLE_KEUANGAN,
        self::ROLE_USER,
        self::ROLE_DEWAN,
        self::ROLE_TA,
    ];

    public const ACCESS_PROFILE_SUPER_ADMIN = 'super_admin';
    public const ACCESS_PROFILE_DEWAN = 'dewan';
    public const ACCESS_PROFILE_SET_DJSN = 'set_djsn';
    public const ACCESS_PROFILE_TATA_USAHA = 'tata_usaha';
    public const ACCESS_PROFILE_PERSIDANGAN = 'persidangan';
    public const ACCESS_PROFILE_PROTHUM = 'prothum';
    public const ACCESS_PROFILE_KEUANGAN = 'keuangan';
    public const ACCESS_PROFILE_TENAGA_AHLI = 'tenaga_ahli';
    public const ACCESS_PROFILE_VIEWER = 'viewer';

    public const ACCESS_PROFILES = [
        self::ACCESS_PROFILE_SUPER_ADMIN,
        self::ACCESS_PROFILE_DEWAN,
        self::ACCESS_PROFILE_SET_DJSN,
        self::ACCESS_PROFILE_TATA_USAHA,
        self::ACCESS_PROFILE_PERSIDANGAN,
        self::ACCESS_PROFILE_PROTHUM,
        self::ACCESS_PROFILE_KEUANGAN,
        self::ACCESS_PROFILE_TENAGA_AHLI,
        self::ACCESS_PROFILE_VIEWER,
    ];

    public const COMMISSION_PME = 'pme';
    public const COMMISSION_KOMJAKUM = 'komjakum';

    public const COMMISSIONS = [
        self::COMMISSION_PME,
        self::COMMISSION_KOMJAKUM,
    ];

    public const STRUCTURE_GROUP_DEWAN = 'dewan';
    public const STRUCTURE_GROUP_SET_DJSN = 'set_djsn';
    public const STRUCTURE_GROUP_SEKRETARIAT = 'sekretariat';
    public const STRUCTURE_GROUP_PENDAMPING = 'pendamping';
    public const STRUCTURE_GROUP_LAINNYA = 'lainnya';

    public const STRUCTURE_GROUPS = [
        self::STRUCTURE_GROUP_DEWAN,
        self::STRUCTURE_GROUP_SET_DJSN,
        self::STRUCTURE_GROUP_SEKRETARIAT,
        self::STRUCTURE_GROUP_PENDAMPING,
        self::STRUCTURE_GROUP_LAINNYA,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'prefix',
        'report_target_label',
        'receives_disposition',
        'disposition_group_label',
        'email',
        'password',
        'role',
        'divisi',
        'division_id',
        'position_id',
        'order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'receives_disposition' => 'boolean',
    ];

    public static function accessProfileOptions(): array
    {
        return [
            self::ACCESS_PROFILE_SUPER_ADMIN => 'Super Admin',
            self::ACCESS_PROFILE_DEWAN => 'Dewan',
            self::ACCESS_PROFILE_SET_DJSN => 'Sekretaris DJSN',
            self::ACCESS_PROFILE_TATA_USAHA => 'Tata Usaha',
            self::ACCESS_PROFILE_PERSIDANGAN => 'Persidangan',
            self::ACCESS_PROFILE_PROTHUM => 'ProtHum',
            self::ACCESS_PROFILE_KEUANGAN => 'Keuangan',
            self::ACCESS_PROFILE_TENAGA_AHLI => 'Tenaga Ahli',
            self::ACCESS_PROFILE_VIEWER => 'Viewer',
        ];
    }

    public static function commissionOptions(): array
    {
        return Division::commissionOptions();
    }

    public static function structureGroupOptions(): array
    {
        return [
            self::STRUCTURE_GROUP_DEWAN => 'Dewan',
            self::STRUCTURE_GROUP_SET_DJSN => 'Sekretaris DJSN',
            self::STRUCTURE_GROUP_SEKRETARIAT => 'Sekretariat',
            self::STRUCTURE_GROUP_PENDAMPING => 'Pendamping Dewan',
            self::STRUCTURE_GROUP_LAINNYA => 'Lainnya',
        ];
    }

    public static function accessProfileLabel(?string $value): string
    {
        return self::accessProfileOptions()[$value] ?? 'Perlu dipilih';
    }

    public static function commissionLabel(?string $value): string
    {
        return Division::commissionLabel($value);
    }

    public static function structureGroupLabel(?string $value): string
    {
        return self::structureGroupOptions()[$value] ?? 'Lainnya';
    }

    public static function legacyRoleFromAccessProfile(?string $profile): string
    {
        return match ($profile) {
            self::ACCESS_PROFILE_SUPER_ADMIN => self::ROLE_SUPER_ADMIN,
            self::ACCESS_PROFILE_DEWAN => self::ROLE_DEWAN,
            self::ACCESS_PROFILE_SET_DJSN => self::ROLE_SECRETARIAT,
            self::ACCESS_PROFILE_TATA_USAHA => self::ROLE_TATA_USAHA,
            self::ACCESS_PROFILE_PERSIDANGAN => self::ROLE_PERSIDANGAN,
            self::ACCESS_PROFILE_PROTHUM => self::ROLE_BAGIAN_UMUM,
            self::ACCESS_PROFILE_KEUANGAN => self::ROLE_KEUANGAN,
            self::ACCESS_PROFILE_TENAGA_AHLI => self::ROLE_TA,
            default => self::ROLE_USER,
        };
    }

    public function isAdmin()
    {
        return $this->isSuperAdmin();
    }

    public function getAdminLevel(): int
    {
        if ($this->isSuperAdmin()) {
            return 0;
        }

        if ($this->canManageActivities()) {
            return 1;
        }

        if ($this->canManagePostActivity()) {
            return 2;
        }

        if ($this->canManageDocumentation()) {
            return 3;
        }

        return 99;
    }

    public function hasRole($roles): bool
    {
        $currentRole = $this->normalizePermissionText($this->role);

        foreach ((array) $roles as $role) {
            if ($currentRole === $this->normalizePermissionText($role)) {
                return true;
            }
        }

        return false;
    }

    // --- Role Helpers ---
    public function isTataUsaha()
    {
        return $this->hasResolvedAccessProfile(self::ACCESS_PROFILE_TATA_USAHA);
    }

    public function isPersidangan()
    {
        return $this->hasResolvedAccessProfile(self::ACCESS_PROFILE_PERSIDANGAN);
    }

    public function isTA()
    {
        return $this->hasResolvedAccessProfile(self::ACCESS_PROFILE_TENAGA_AHLI);
    }

    public function isSuperAdmin()
    {
        return $this->hasResolvedAccessProfile(self::ACCESS_PROFILE_SUPER_ADMIN);
    }

    public function isPrimarySuperAdmin(): bool
    {
        if (!$this->isSuperAdmin()) {
            return false;
        }

        $email = mb_strtolower(trim((string) $this->email));

        return $this->id === 1 || $email === 'admin@djsn.com';
    }

    public function isDewan()
    {
        return $this->hasResolvedAccessProfile(self::ACCESS_PROFILE_DEWAN);
    }

    public function isSetDjsn()
    {
        return $this->hasResolvedAccessProfile(self::ACCESS_PROFILE_SET_DJSN);
    }

    public function isProtHum()
    {
        return $this->hasResolvedAccessProfile(self::ACCESS_PROFILE_PROTHUM);
    }

    public function isKeuangan()
    {
        return $this->hasResolvedAccessProfile(self::ACCESS_PROFILE_KEUANGAN);
    }

    public function isSekretarisDjsnPosition()
    {
        $position = $this->resolvePositionModel();
        $positionName = $this->normalizePermissionText($position?->name);

        return $position?->code === 'sekretaris_djsn'
            || str_contains($positionName, 'SEKRETARIS DJSN')
            || $this->divisionContainsAll(['SEKRETARIS', 'DJSN']);
    }

    public function isKepalaBagUmumPosition()
    {
        $position = $this->resolvePositionModel();
        $positionName = $this->normalizePermissionText($position?->name);

        return $position?->code === 'kabag_umum'
            || (str_contains($positionName, 'KEPALA') && str_contains($positionName, 'BAGIAN') && str_contains($positionName, 'UMUM'))
            || $this->divisionContainsAll(['KEPALA', 'BAGIAN', 'UMUM']);
    }

    public function isKasubagTuRumahTanggaPosition()
    {
        $position = $this->resolvePositionModel();
        $positionName = $this->normalizePermissionText($position?->name);

        return $position?->code === 'kasubag_tu_rt'
            || (
                str_contains($positionName, 'SUB')
                && str_contains($positionName, 'TATA USAHA')
                && (str_contains($positionName, 'RUMAH') || str_contains($positionName, 'RT'))
            )
            || ($this->divisionContainsAll(['TATA', 'USAHA']) && $this->divisionContainsAny(['SUB', 'RUMAH']));
    }

    public function isKabagPersidanganPosition()
    {
        $position = $this->resolvePositionModel();
        $positionName = $this->normalizePermissionText($position?->name);

        return $position?->code === 'kabag_persidangan'
            || (str_contains($positionName, 'PERSIDANGAN') && $this->containsAnyKeyword($positionName, ['KEPALA', 'PLT', 'KABAG']))
            || ($this->divisionContainsAll(['PERSIDANGAN']) && $this->divisionContainsAny(['KEPALA', 'PLT']));
    }

    public function isKasubagProtokolHumasPosition()
    {
        $position = $this->resolvePositionModel();
        $positionName = $this->normalizePermissionText($position?->name);

        return $position?->code === 'kasubag_protokol_humas'
            || (
                $this->containsAnyKeyword($positionName, ['PROTOKOL', 'HUMAS', 'KEHUMASAN'])
                && $this->containsAnyKeyword($positionName, ['SUB', 'KASUBAG'])
            )
            || ($this->divisionContainsAny(['PROTOKOL']) && $this->divisionContainsAny(['SUB', 'KEHUMASAN', 'HUMAS']));
    }

    public function isProtokolHumasUnit()
    {
        return $this->divisionContainsAll(['PROTOKOL', 'HUMAS']);
    }

    public function isPersidanganUnit()
    {
        return $this->divisionContainsAny(['PERSIDANGAN']);
    }

    public function canAccessAdminArea()
    {
        return $this->isSuperAdmin();
    }

    public function canAccessH1Report()
    {
        return $this->canManageActivities();
    }

    // --- Capability Helpers ---
    public function canManageActivities()
    {
        return $this->isSuperAdmin() || $this->isTataUsaha();
    }

    public function canUploadAssignment()
    {
        return $this->canManageActivities();
    }

    public function canManagePostActivity()
    {
        return $this->isSuperAdmin() || $this->isPersidangan();
    }

    public function canManageFollowUp()
    {
        return $this->canManagePostActivity();
    }

    public function canManageTopics()
    {
        return $this->canManagePostActivity();
    }

    public function canManageDocumentation()
    {
        return $this->isSuperAdmin() || $this->isProtHum();
    }

    public function canReceiveDisposition()
    {
        $position = $this->resolvePositionModel();

        if ($this->receives_disposition !== null) {
            return (bool) $this->receives_disposition;
        }

        if ($position?->receives_disposition !== null) {
            return (bool) $position->receives_disposition;
        }

        return $this->isDewan()
            || $this->division?->structure_group === Division::STRUCTURE_GROUP_SECRETARY
            || $this->usesLegacyDispositionRule();
    }

    public function canViewAllActivities()
    {
        return $this->isSuperAdmin()
            || $this->canManageActivities()
            || $this->canManageDocumentation()
            || $this->isKeuangan();
    }

    public function canViewInternalActivitiesWithoutDisposition(): bool
    {
        return $this->isDewan() || $this->isSetDjsn();
    }

    public function canViewActivity(Activity $activity): bool
    {
        if ($this->canViewAllActivities()) {
            return true;
        }

        $dispositionTo = is_array($activity->disposition_to) ? $activity->disposition_to : [];
        $pic = is_array($activity->pic) ? $activity->pic : array_filter([(string) $activity->pic]);

        if (in_array($this->name, $dispositionTo, true) || in_array($this->name, $pic, true)) {
            return true;
        }

        if (
            $this->canViewInternalActivitiesWithoutDisposition()
            && $activity->type === 'internal'
            && !$activity->hasDispositionRecipients()
        ) {
            return true;
        }

        if (!($this->isTA() || $this->isPersidangan())) {
            return false;
        }

        $commissionDewanNames = $this->getCommissionDewanNames();
        if (empty($commissionDewanNames)) {
            return false;
        }

        return !empty(array_intersect($dispositionTo, $commissionDewanNames));
    }

    public function getCommissionKeys(): array
    {
        $division = $this->division;

        $divisionCode = Division::normalizeCommissionCode($division?->commission_code);
        if ($divisionCode !== null) {
            return [$divisionCode];
        }

        if ($division?->is_commission) {
            $generatedCode = Division::normalizeCommissionCode(
                $division->commission_code ?: $division->name
            );

            if ($generatedCode !== null) {
                return [$generatedCode];
            }
        }

        $legacyKeys = $this->deriveLegacyCommissionKeys();
        if (!empty($legacyKeys)) {
            return $legacyKeys;
        }

        $resolved = Division::normalizeCommissionCode($this->resolved_commission_code ?? null);
        if ($resolved !== null) {
            return [$resolved];
        }

        return [];
    }

    public function getCommissionDewanNames(): array
    {
        $commissionKeys = $this->getCommissionKeys();

        if (empty($commissionKeys)) {
            return [];
        }

        return self::query()
            ->with(['division', 'position'])
            ->get()
            ->filter(function (self $user) use ($commissionKeys) {
                if (!$user->isDewan()) {
                    return false;
                }

                return !empty(array_intersect($commissionKeys, $user->getCommissionKeys()));
            })
            ->pluck('name')
            ->values()
            ->all();
    }

    public function getResolvedAccessProfileAttribute(): string
    {
        $positionProfile = trim((string) ($this->resolvePositionModel()?->access_profile ?? ''));
        if ($positionProfile !== '') {
            return $positionProfile;
        }

        $divisionProfile = trim((string) ($this->division?->access_profile ?? ''));
        if ($divisionProfile !== '') {
            return $divisionProfile;
        }

        return $this->resolveLegacyAccessProfile();
    }

    public function getResolvedCommissionCodeAttribute(): ?string
    {
        $division = $this->division;
        $divisionCode = Division::normalizeCommissionCode($division?->commission_code);

        if ($divisionCode !== null) {
            return $divisionCode;
        }

        if ($division?->is_commission) {
            return Division::normalizeCommissionCode(
                $division->commission_code ?: $division->name
            );
        }

        $commissionKeys = $this->deriveLegacyCommissionKeys();

        return $commissionKeys[0] ?? null;
    }

    public function getResolvedCommissionLabelAttribute(): string
    {
        $division = $this->division;

        if ($division?->is_commission) {
            return $division->display_name;
        }

        if ($this->resolved_commission_code) {
            return Division::commissionLabel($this->resolved_commission_code);
        }

        return '-';
    }

    public function getResolvedAccessProfileLabelAttribute(): string
    {
        return self::accessProfileLabel($this->resolved_access_profile);
    }

    public function getDisplayRoleLabelAttribute(): string
    {
        return self::accessProfileLabel($this->resolved_access_profile);
    }

    public function getManagementSectionKeyAttribute(): string
    {
        if ($this->isSuperAdmin()) {
            return 'super_admin';
        }

        if ($this->division?->structure_group) {
            return $this->division->structure_group;
        }

        if ($this->isDewan()) {
            return Division::STRUCTURE_GROUP_DEWAN;
        }

        if ($this->isSetDjsn()) {
            return Division::STRUCTURE_GROUP_SECRETARY;
        }

        if ($this->isTA() || $this->isPersidangan()) {
            return Division::STRUCTURE_GROUP_SUPPORT;
        }

        return Division::STRUCTURE_GROUP_SECRETARIAT;
    }

    public function getManagementGroupLabelAttribute(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Super Admin';
        }

        $division = $this->division;
        $unitLabel = trim((string) ($division?->display_name ?? $this->divisi));

        return match ($this->management_section_key) {
            Division::STRUCTURE_GROUP_DEWAN => $unitLabel !== '' ? 'Dewan - ' . $unitLabel : 'Dewan',
            Division::STRUCTURE_GROUP_SECRETARY => 'Sekretaris DJSN',
            Division::STRUCTURE_GROUP_SECRETARIAT => $unitLabel !== '' ? 'Sekretariat DJSN - ' . $unitLabel : 'Sekretariat DJSN',
            Division::STRUCTURE_GROUP_SUPPORT => $this->buildSupportManagementGroupLabel(),
            default => $this->display_role_label,
        };
    }

    public function getManagementSortOrderAttribute(): int
    {
        if ($this->isSuperAdmin()) {
            return 0;
        }

        $sectionOrder = match ($this->management_section_key) {
            Division::STRUCTURE_GROUP_DEWAN => 1,
            Division::STRUCTURE_GROUP_SECRETARY => 2,
            Division::STRUCTURE_GROUP_SECRETARIAT => 3,
            Division::STRUCTURE_GROUP_SUPPORT => 4,
            default => 9,
        };

        $divisionOrder = $this->division?->order ?? 999;
        $positionOrder = $this->position?->order ?? 999;

        return ($sectionOrder * 100000) + ($divisionOrder * 100) + $positionOrder;
    }

    /**
     * Send the password reset notification using Indonesian language.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function matchesDivisionKeywords(array $needles): bool
    {
        return $this->divisionContainsAll($needles);
    }

    public function getDispositionSecretariatLabelAttribute(): ?string
    {
        $configuredLabel = trim((string) ($this->resolved_report_target_label ?? ''));
        if ($configuredLabel !== '' && !$this->isDewan()) {
            return $configuredLabel;
        }

        if ($this->isSekretarisDjsnPosition()) {
            return 'Sekretaris DJSN';
        }

        if ($this->isKepalaBagUmumPosition()) {
            return 'Kepala Bag. Umum';
        }

        if ($this->isKabagPersidanganPosition()) {
            return 'Plt/Kabag Persidangan';
        }

        if ($this->isKasubagTuRumahTanggaPosition()) {
            return 'Kepala Sub. Bag. TU & Rumah Tangga';
        }

        if ($this->isKasubagProtokolHumasPosition()) {
            return 'Kepala Sub. Bag. Protokol & Humas';
        }

        return null;
    }

    public function getDispositionGroupLabelAttribute(): string
    {
        $position = $this->resolvePositionModel();

        $override = trim((string) ($this->attributes['disposition_group_label'] ?? ''));
        if ($override !== '') {
            return $this->normalizeDispositionGroupLabel($override);
        }

        $positionLabel = trim((string) ($position?->disposition_group_label ?? ''));
        if ($positionLabel !== '') {
            return $this->normalizeDispositionGroupLabel($positionLabel);
        }

        return $this->normalizeDispositionGroupLabel($this->deriveLegacyDispositionGroupLabel());
    }

    public function getResolvedReportTargetLabelAttribute(): ?string
    {
        $position = $this->resolvePositionModel();

        $customLabel = trim((string) ($this->report_target_label ?? ''));
        if ($customLabel !== '') {
            return $customLabel;
        }

        $positionLabel = trim((string) ($position?->report_target_label ?? ''));
        if ($positionLabel !== '') {
            return $positionLabel;
        }

        if (!$this->isDewan() && $this->canReceiveDisposition()) {
            $positionName = trim((string) ($position?->name ?? ''));
            if ($positionName !== '') {
                return $positionName;
            }

            $groupLabel = trim((string) $this->disposition_group_label);
            if ($groupLabel !== '') {
                return $groupLabel;
            }
        }

        $divisionName = strtoupper(trim((string) ($this->division?->name ?? $this->divisi)));
        $divisionCategory = strtoupper(trim((string) ($this->division?->category ?? '')));

        if (
            $divisionName === 'KETUA DJSN'
            || $divisionCategory === 'KETUA DJSN'
            || str_contains($divisionName, 'KETUA DJSN')
        ) {
            return 'Ketua DJSN';
        }

        if ($this->isDewan()) {
            return $this->name ?: null;
        }

        return null;
    }

    private function hasResolvedAccessProfile(string $profile): bool
    {
        return $this->resolved_access_profile === $profile;
    }

    private function resolveLegacyAccessProfile(): string
    {
        if ($this->hasRole([self::ROLE_SUPER_ADMIN, self::ROLE_LEGACY_ADMIN])) {
            return self::ACCESS_PROFILE_SUPER_ADMIN;
        }

        if ($this->division?->structure_group === Division::STRUCTURE_GROUP_SECRETARY) {
            return self::ACCESS_PROFILE_SET_DJSN;
        }

        if ($this->hasRole(self::ROLE_DEWAN) || $this->resolvePositionModel()?->structure_group === self::STRUCTURE_GROUP_DEWAN) {
            return self::ACCESS_PROFILE_DEWAN;
        }

        if ($this->isSekretarisDjsnPosition()) {
            return self::ACCESS_PROFILE_SET_DJSN;
        }

        if ($this->hasRole(self::ROLE_TATA_USAHA) || $this->isKepalaBagUmumPosition() || $this->isKasubagTuRumahTanggaPosition()) {
            return self::ACCESS_PROFILE_TATA_USAHA;
        }

        if ($this->hasRole(self::ROLE_PERSIDANGAN) || $this->isKabagPersidanganPosition() || $this->isPersidanganUnit()) {
            return self::ACCESS_PROFILE_PERSIDANGAN;
        }

        if ($this->hasRole([self::ROLE_BAGIAN_UMUM]) || $this->isKasubagProtokolHumasPosition() || $this->isProtokolHumasUnit()) {
            return self::ACCESS_PROFILE_PROTHUM;
        }

        if ($this->hasRole(self::ROLE_KEUANGAN)) {
            return self::ACCESS_PROFILE_KEUANGAN;
        }

        if ($this->hasRole(self::ROLE_TA)) {
            return self::ACCESS_PROFILE_TENAGA_AHLI;
        }

        return self::ACCESS_PROFILE_VIEWER;
    }

    private function deriveLegacyCommissionKeys(): array
    {
        $sources = [
            $this->division?->name,
            $this->division?->short_label,
            $this->divisi,
            $this->division?->category,
        ];

        $keys = [];

        foreach ($sources as $source) {
            $normalized = $this->normalizePermissionText($source);

            if ($normalized === '') {
                continue;
            }

            $inferredCode = Division::inferCommissionCode($normalized);
            if ($inferredCode !== null) {
                $keys[] = $inferredCode;
            }
        }

        return array_values(array_unique($keys));
    }

    private function containsAnyKeyword(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $this->normalizePermissionText($needle))) {
                return true;
            }
        }

        return false;
    }

    private function divisionContainsAll(array $needles): bool
    {
        $divisionName = $this->normalizePermissionText($this->division?->name ?? $this->divisi);

        if ($divisionName === '') {
            return false;
        }

        foreach ($needles as $needle) {
            if (!str_contains($divisionName, $this->normalizePermissionText($needle))) {
                return false;
            }
        }

        return true;
    }

    private function divisionContainsAny(array $needles): bool
    {
        $divisionName = $this->normalizePermissionText($this->division?->name ?? $this->divisi);

        if ($divisionName === '') {
            return false;
        }

        foreach ($needles as $needle) {
            if (str_contains($divisionName, $this->normalizePermissionText($needle))) {
                return true;
            }
        }

        return false;
    }

    private function usesLegacyDispositionRule(): bool
    {
        return $this->isDewan()
            || $this->isSekretarisDjsnPosition()
            || $this->isKepalaBagUmumPosition()
            || $this->isKabagPersidanganPosition()
            || $this->isKasubagTuRumahTanggaPosition()
            || $this->isKasubagProtokolHumasPosition();
    }

    private function deriveLegacyDispositionGroupLabel(): string
    {
        if ($this->matchesDivisionKeywords(['KETUA', 'DJSN'])) {
            return 'Ketua DJSN';
        }

        $commissionKeys = $this->getCommissionKeys();
        if (!empty($commissionKeys)) {
            return Division::commissionLabel($commissionKeys[0]);
        }

        if ($this->isDewan()) {
            return 'Dewan';
        }

        return 'Sekretaris DJSN';
    }

    private function normalizeDispositionGroupLabel(string $label): string
    {
        $normalized = $this->normalizePermissionText($label);

        if (in_array($normalized, [
            'PIMPINAN SEKRETARIAT DJSN',
            'SEKRETARIS DJSN',
            'SEKRETARIAT DJSN',
            'SET DJSN',
        ], true)) {
            return 'Sekretaris DJSN';
        }

        return trim($label);
    }

    private function buildSupportManagementGroupLabel(): string
    {
        $baseLabel = self::accessProfileLabel($this->resolved_access_profile);

        if ($this->resolved_commission_label !== '-') {
            return $baseLabel . ' - ' . $this->resolved_commission_label;
        }

        return $baseLabel;
    }

    private function resolvePositionModel(): ?Position
    {
        if ($this->relationLoaded('position')) {
            $relation = $this->getRelation('position');

            return $relation instanceof Position ? $relation : null;
        }

        if (!$this->position_id || static::getConnectionResolver() === null) {
            return null;
        }

        return $this->position;
    }

    private function normalizePermissionText(?string $value): string
    {
        $normalized = mb_strtoupper(trim((string) $value));
        $normalized = preg_replace('/[^[:alnum:]]+/u', ' ', $normalized);

        return trim((string) preg_replace('/\s+/u', ' ', $normalized));
    }
}
