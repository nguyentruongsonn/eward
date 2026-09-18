<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addUniqueEmailConstraint();
        $this->addRatingCitizenConstraint();
    }

    public function down(): void
    {
        if (Schema::hasTable('danhgia')) {
            $foreignKey = collect(Schema::getForeignKeys('danhgia'))
                ->first(fn (array $key): bool => in_array('IDCD', $key['columns'], true));

            if ($foreignKey !== null) {
                $foreignKeyName = $foreignKey['name'];
                Schema::table('danhgia', function (Blueprint $table) use ($foreignKeyName): void {
                    $table->dropForeign($foreignKeyName);
                });
            }
        }

        if (Schema::hasTable('nguoi')) {
            $index = collect(Schema::getIndexes('nguoi'))
                ->first(fn (array $key): bool => $key['name'] === 'nguoi_email_unique');

            if ($index !== null) {
                Schema::table('nguoi', function (Blueprint $table): void {
                    $table->dropUnique('nguoi_email_unique');
                });
            }
        }
    }

    private function addUniqueEmailConstraint(): void
    {
        if (! Schema::hasTable('nguoi') || ! Schema::hasColumn('nguoi', 'email')) {
            return;
        }

        $hasDuplicates = DB::table('nguoi')
            ->selectRaw('LOWER(TRIM(email)) as normalized_email')
            ->whereNotNull('email')
            ->groupByRaw('LOWER(TRIM(email))')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasDuplicates) {
            throw new RuntimeException(
                'Cannot add nguoi_email_unique: duplicate emails exist. Run php artisan data:integrity-report and resolve duplicates first.',
            );
        }

        $index = collect(Schema::getIndexes('nguoi'))
            ->first(fn (array $key): bool => $key['name'] === 'nguoi_email_unique');

        if ($index === null) {
            Schema::table('nguoi', function (Blueprint $table): void {
                $table->unique('email', 'nguoi_email_unique');
            });
        }
    }

    private function addRatingCitizenConstraint(): void
    {
        if (! Schema::hasTable('danhgia')
            || ! Schema::hasColumn('danhgia', 'IDCD')
            || ! Schema::hasTable('congdan')) {
            return;
        }

        $hasOrphans = DB::table('danhgia as rating')
            ->leftJoin('congdan as citizen', 'citizen.IDCD', '=', 'rating.IDCD')
            ->whereNull('citizen.IDCD')
            ->exists();
        $hasNegativeIds = DB::table('danhgia')->where('IDCD', '<', 0)->exists();

        if ($hasOrphans || $hasNegativeIds) {
            throw new RuntimeException(
                'Cannot add danhgia_idcd_foreign: orphan or invalid citizen references exist. Run php artisan data:integrity-report and resolve them first.',
            );
        }

        $foreignKey = collect(Schema::getForeignKeys('danhgia'))
            ->first(fn (array $key): bool => in_array('IDCD', $key['columns'], true));

        if ($foreignKey !== null) {
            return;
        }

        Schema::table('danhgia', function (Blueprint $table): void {
            $table->unsignedInteger('IDCD')->change();
        });

        Schema::table('danhgia', function (Blueprint $table): void {
            $table->foreign('IDCD', 'danhgia_idcd_foreign')
                ->references('IDCD')
                ->on('congdan')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
