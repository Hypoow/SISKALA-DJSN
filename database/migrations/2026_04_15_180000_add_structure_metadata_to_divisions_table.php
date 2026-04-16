<?php

use App\Models\Division;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->string('short_label')->nullable()->after('name');
            $table->string('structure_group')->nullable()->after('category');
            $table->string('description')->nullable()->after('commission_code');
            $table->boolean('is_commission')->default(false)->after('description');
        });

        $divisions = DB::table('divisions')
            ->select('id', 'name', 'category', 'access_profile', 'commission_code')
            ->get();

        foreach ($divisions as $division) {
            $name = trim((string) $division->name);
            $accessProfile = trim((string) $division->access_profile);
            $commissionCode = Division::normalizeCommissionCode($division->commission_code)
                ?? Division::inferCommissionCode($name);

            $structureGroup = Division::STRUCTURE_GROUP_SECRETARIAT;
            $isCommission = false;
            $shortLabel = null;

            if ($this->containsAny($name, ['KETUA DJSN']) || $division->category === 'Ketua DJSN') {
                $structureGroup = Division::STRUCTURE_GROUP_DEWAN;
                $shortLabel = 'Ketua DJSN';
            } elseif ($division->category === 'Komisi' || $accessProfile === 'dewan') {
                $structureGroup = Division::STRUCTURE_GROUP_DEWAN;
                $isCommission = true;
                $commissionCode = $commissionCode ?? Division::normalizeCommissionCode($name);
                $shortLabel = $this->defaultShortLabel($name, $commissionCode);
            } elseif ($accessProfile === 'set_djsn' || $this->containsAny($name, ['SEKRETARIS DJSN'])) {
                $structureGroup = Division::STRUCTURE_GROUP_SECRETARY;
                $shortLabel = 'Sekretaris DJSN';
            } elseif (in_array($accessProfile, ['persidangan', 'tenaga_ahli'], true)) {
                $structureGroup = Division::STRUCTURE_GROUP_SUPPORT;
                $shortLabel = $this->defaultShortLabel($name, $commissionCode);
            } else {
                $shortLabel = $this->defaultShortLabel($name, $commissionCode);
            }

            DB::table('divisions')
                ->where('id', $division->id)
                ->update([
                    'structure_group' => $structureGroup,
                    'commission_code' => $commissionCode,
                    'short_label' => $shortLabel,
                    'is_commission' => $isCommission,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn([
                'short_label',
                'structure_group',
                'description',
                'is_commission',
            ]);
        });
    }

    private function defaultShortLabel(string $name, ?string $commissionCode): ?string
    {
        if ($commissionCode === 'komjakum') {
            return 'Komjakum';
        }

        if ($commissionCode === 'pme') {
            return 'Komisi PME';
        }

        if ($this->containsAny($name, ['PROTOKOL', 'HUMAS'])) {
            return 'ProtHum';
        }

        return null;
    }

    private function containsAny(?string $value, array $needles): bool
    {
        $normalized = mb_strtoupper(trim((string) $value));

        foreach ($needles as $needle) {
            if (str_contains($normalized, mb_strtoupper(trim($needle)))) {
                return true;
            }
        }

        return false;
    }
};
