<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Division extends Model
{
    use HasFactory;

    public const STRUCTURE_GROUP_DEWAN = 'dewan';
    public const STRUCTURE_GROUP_SECRETARY = 'sekretaris_djsn';
    public const STRUCTURE_GROUP_SECRETARIAT = 'sekretariat_djsn';
    public const STRUCTURE_GROUP_SUPPORT = 'pendamping_dewan';
    public const STRUCTURE_GROUP_OTHER = 'lainnya';

    public const STRUCTURE_GROUPS = [
        self::STRUCTURE_GROUP_DEWAN,
        self::STRUCTURE_GROUP_SECRETARY,
        self::STRUCTURE_GROUP_SECRETARIAT,
        self::STRUCTURE_GROUP_SUPPORT,
        self::STRUCTURE_GROUP_OTHER,
    ];

    protected $fillable = [
        'name',
        'short_label',
        'category',
        'structure_group',
        'description',
        'access_profile',
        'commission_code',
        'is_commission',
        'order',
    ];

    protected $casts = [
        'is_commission' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $label = trim((string) ($this->short_label ?: $this->name));

        return $label !== '' ? $label : 'Tanpa Nama';
    }

    public function getNormalizedCommissionCodeAttribute(): ?string
    {
        return self::normalizeCommissionCode($this->commission_code);
    }

    public function getStructureGroupLabelAttribute(): string
    {
        return self::structureGroupLabel($this->structure_group);
    }

    public function scopeCommissionDefinitions($query)
    {
        return $query->where('is_commission', true);
    }

    public static function structureGroupOptions(): array
    {
        return [
            self::STRUCTURE_GROUP_DEWAN => 'Dewan',
            self::STRUCTURE_GROUP_SECRETARY => 'Sekretaris DJSN',
            self::STRUCTURE_GROUP_SECRETARIAT => 'Sekretariat DJSN',
            self::STRUCTURE_GROUP_SUPPORT => 'Pendamping Dewan',
            self::STRUCTURE_GROUP_OTHER => 'Lainnya',
        ];
    }

    public static function structureGroupLabel(?string $value): string
    {
        return self::structureGroupOptions()[$value] ?? 'Lainnya';
    }

    public static function structureGroupSortOrder(?string $value): int
    {
        return match ($value) {
            self::STRUCTURE_GROUP_DEWAN => 1,
            self::STRUCTURE_GROUP_SECRETARY => 2,
            self::STRUCTURE_GROUP_SECRETARIAT => 3,
            self::STRUCTURE_GROUP_SUPPORT => 4,
            default => 99,
        };
    }

    public static function legacyCategoryFor(?string $structureGroup, bool $isCommission = false, ?string $name = null): string
    {
        $normalizedName = mb_strtoupper(trim((string) $name));

        if ($structureGroup === self::STRUCTURE_GROUP_DEWAN) {
            if ($isCommission) {
                return 'Komisi';
            }

            return str_contains($normalizedName, 'KETUA DJSN') ? 'Ketua DJSN' : 'Komisi';
        }

        return 'Sekretariat DJSN';
    }

    public static function commissionOptions(): array
    {
        if (static::getConnectionResolver() === null) {
            return [
                'pme' => 'Komisi PME',
                'komjakum' => 'Komjakum',
            ];
        }

        return static::query()
            ->commissionDefinitions()
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (self $division) {
                $code = $division->normalized_commission_code
                    ?? self::normalizeCommissionCode($division->short_label ?: $division->name);

                if ($code === null) {
                    return [];
                }

                return [$code => $division->display_name];
            })
            ->all();
    }

    public static function commissionLabel(?string $code, ?string $fallback = null): string
    {
        $normalizedCode = self::normalizeCommissionCode($code);

        if ($normalizedCode === null) {
            return $fallback ?: '-';
        }

        $options = self::commissionOptions();

        return $options[$normalizedCode] ?? $fallback ?? Str::headline(str_replace('_', ' ', $normalizedCode));
    }

    public static function normalizeCommissionCode(?string $value): ?string
    {
        $normalized = Str::of((string) $value)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/u', '_')
            ->trim('_')
            ->value();

        return $normalized !== '' ? $normalized : null;
    }

    public static function inferCommissionCode(?string $value): ?string
    {
        $normalized = mb_strtoupper(trim((string) $value));

        if ($normalized === '') {
            return null;
        }

        if (str_contains($normalized, 'PME') || str_contains($normalized, 'MONITORING')) {
            return 'pme';
        }

        if (str_contains($normalized, 'KOMJAKUM') || str_contains($normalized, 'KEBIJAKAN')) {
            return 'komjakum';
        }

        return null;
    }

    public static function findCommissionDefinition(?string $code): ?self
    {
        $normalizedCode = self::normalizeCommissionCode($code);

        if ($normalizedCode === null || static::getConnectionResolver() === null) {
            return null;
        }

        return static::query()
            ->commissionDefinitions()
            ->where('commission_code', $normalizedCode)
            ->first();
    }

    public static function findByDisplayLabel(?string $label): ?self
    {
        $normalizedLabel = trim((string) $label);

        if ($normalizedLabel === '' || static::getConnectionResolver() === null) {
            return null;
        }

        return static::query()
            ->where(function ($query) use ($normalizedLabel) {
                $query->where('name', $normalizedLabel)
                    ->orWhere('short_label', $normalizedLabel);
            })
            ->orderBy('order')
            ->first();
    }
}
