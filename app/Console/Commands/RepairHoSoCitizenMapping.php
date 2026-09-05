<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairHoSoCitizenMapping extends Command
{
    protected $signature = 'hoso:repair-citizen-mapping {--execute : Apply the reported changes}';

    protected $description = 'Report or repair hồ sơ whose citizen mapping is missing or invalid';

    public function handle(): int
    {
        $rows = DB::table('hosoxuly as h')
            ->join('nguoi as n', DB::raw('LOWER(h.email)'), '=', DB::raw('LOWER(n.email)'))
            ->join('congdan as c', 'c.IDnguoiDung', '=', 'n.IDnguoiDung')
            ->where(function ($query): void {
                $query->whereColumn('h.IDCD', '!=', 'c.IDCD')
                    ->orWhereNull('h.IDCD');
            })
            ->select('h.maHSXL', 'h.IDCD as current_id', 'c.IDCD as expected_id')
            ->get();

        if ($rows->isEmpty()) {
            $this->info('Không có hồ sơ cần sửa.');

            return self::SUCCESS;
        }

        foreach ($rows as $row) {
            $this->line("{$row->maHSXL}: {$row->current_id} -> {$row->expected_id}");
        }

        if (! $this->option('execute')) {
            $this->warn('Dry-run: thêm --execute sau khi đã backup và review danh sách.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($rows): void {
            foreach ($rows as $row) {
                DB::table('hosoxuly')->where('maHSXL', $row->maHSXL)->update(['IDCD' => $row->expected_id]);
            }
        });
        $this->info('Đã sửa '.$rows->count().' hồ sơ.');

        return self::SUCCESS;
    }
}
