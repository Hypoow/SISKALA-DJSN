<?php

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
            $table->string('access_profile')->nullable()->after('category');
            $table->string('commission_code')->nullable()->after('access_profile');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->string('structure_group')->nullable()->after('code');
            $table->string('access_profile')->nullable()->after('structure_group');
        });

        $divisions = DB::table('divisions')->select('id', 'name', 'category')->get();
        foreach ($divisions as $division) {
            $normalized = $this->normalize($division->name . ' ' . $division->category);
            $updates = [];

            if ($this->containsAny($normalized, ['PME', 'MONITORING'])) {
                $updates['commission_code'] = 'pme';
            } elseif ($this->containsAny($normalized, ['KOMJAKUM', 'KEBIJAKAN'])) {
                $updates['commission_code'] = 'komjakum';
            }

            if ($this->containsAny($normalized, ['TENAGA AHLI'])) {
                $updates['access_profile'] = 'tenaga_ahli';
            } elseif ($this->containsAny($normalized, ['PERSIDANGAN'])) {
                $updates['access_profile'] = 'persidangan';
            } elseif ($this->containsAny($normalized, ['TATA USAHA', 'RUMAH TANGGA'])) {
                $updates['access_profile'] = 'tata_usaha';
            } elseif ($this->containsAny($normalized, ['PROTOKOL', 'HUMAS'])) {
                $updates['access_profile'] = 'prothum';
            } elseif ($this->containsAny($normalized, ['KEUANGAN'])) {
                $updates['access_profile'] = 'keuangan';
            }

            if (!empty($updates)) {
                DB::table('divisions')->where('id', $division->id)->update($updates);
            }
        }

        $positions = DB::table('positions')->select('id', 'code', 'name')->get();
        foreach ($positions as $position) {
            $normalized = $this->normalize($position->name . ' ' . $position->code);
            $updates = [];

            if ($this->containsAny($normalized, ['ANGGOTA DEWAN', 'KETUA KOMISI', 'WAKIL KOMISI', 'KETUA DJSN'])) {
                $updates['structure_group'] = 'dewan';
                $updates['access_profile'] = 'dewan';
            } elseif ($this->containsAny($normalized, ['SEKRETARIS DJSN', 'KABAG', 'KASUBAG'])) {
                $updates['structure_group'] = 'set_djsn';

                if ($this->containsAny($normalized, ['SEKRETARIS DJSN'])) {
                    $updates['access_profile'] = 'set_djsn';
                }
            } elseif ($this->containsAny($normalized, ['TENAGA AHLI'])) {
                $updates['structure_group'] = 'pendamping';
                $updates['access_profile'] = 'tenaga_ahli';
            } elseif ($this->containsAny($normalized, ['PERSIDANGAN'])) {
                $updates['structure_group'] = 'sekretariat';
                $updates['access_profile'] = 'persidangan';
            } elseif ($this->containsAny($normalized, ['TATA USAHA', 'RUMAH TANGGA'])) {
                $updates['structure_group'] = 'sekretariat';
                $updates['access_profile'] = 'tata_usaha';
            } elseif ($this->containsAny($normalized, ['PROTOKOL', 'HUMAS'])) {
                $updates['structure_group'] = 'sekretariat';
                $updates['access_profile'] = 'prothum';
            } elseif ($this->containsAny($normalized, ['KEUANGAN'])) {
                $updates['structure_group'] = 'sekretariat';
                $updates['access_profile'] = 'keuangan';
            }

            if (!empty($updates)) {
                DB::table('positions')->where('id', $position->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn([
                'structure_group',
                'access_profile',
            ]);
        });

        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn([
                'access_profile',
                'commission_code',
            ]);
        });
    }

    private function normalize(?string $value): string
    {
        $normalized = mb_strtoupper(trim((string) $value));
        $normalized = preg_replace('/[^[:alnum:]]+/u', ' ', $normalized);

        return trim((string) preg_replace('/\s+/u', ' ', $normalized));
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $this->normalize($needle))) {
                return true;
            }
        }

        return false;
    }
};
