<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReportDataIntegrity extends Command
{
    protected $signature = 'data:integrity-report';

    protected $description = 'Report duplicate identity data and orphaned operational records';

    public function handle(): int
    {
        $duplicates = DB::table('nguoi')
            ->selectRaw('LOWER(TRIM(email)) as normalized_email, COUNT(*) as total')
            ->whereNotNull('email')
            ->groupByRaw('LOWER(TRIM(email))')
            ->having('total', '>', 1)
            ->orderByDesc('total')
            ->get();
        $duplicateCccd = DB::table('nguoi')
            ->selectRaw('TRIM(maCCCD) as normalized_cccd, COUNT(*) as total')
            ->whereNotNull('maCCCD')
            ->whereRaw("TRIM(maCCCD) <> ''")
            ->groupByRaw('TRIM(maCCCD)')
            ->having('total', '>', 1)
            ->orderByDesc('total')
            ->get();

        $orphanApplications = DB::table('hosoxuly as h')
            ->leftJoin('congdan as c', 'c.IDCD', '=', 'h.IDCD')
            ->whereNull('c.IDCD')
            ->count();
        $orphanRatings = DB::table('danhgia as rating')
            ->leftJoin('congdan as c', 'c.IDCD', '=', 'rating.IDCD')
            ->whereNull('c.IDCD')
            ->count();
        $negativeRatingIds = DB::table('danhgia')
            ->where('IDCD', '<', 0)
            ->count();

        $this->line('Duplicate emails: '.$duplicates->count());
        foreach ($duplicates as $duplicate) {
            $this->line("- {$duplicate->normalized_email}: {$duplicate->total}");
        }
        $this->line('Duplicate CCCD: '.$duplicateCccd->count());
        foreach ($duplicateCccd as $duplicate) {
            $this->line("- {$duplicate->normalized_cccd}: {$duplicate->total}");
        }
        $this->line('Orphan applications: '.$orphanApplications);
        $this->line('Orphan ratings: '.$orphanRatings);
        $this->line('Invalid rating citizen IDs: '.$negativeRatingIds);

        return ($duplicates->isNotEmpty()
            || $duplicateCccd->isNotEmpty()
            || $orphanApplications > 0
            || $orphanRatings > 0
            || $negativeRatingIds > 0)
            ? self::FAILURE
            : self::SUCCESS;
    }
}
