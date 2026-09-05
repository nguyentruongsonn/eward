<?php

namespace App\Services\Reports;

use App\Services\Payments\AdminPaymentHistoryService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminPaymentExportService
{
    public function __construct(private readonly AdminPaymentHistoryService $history) {}

    /** @return array{path: string, filename: string} */
    public function history(array $filters = []): array
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lịch sử thanh toán');
        $sheet->fromArray([
            'Mã GD', 'Số GD', 'Người thanh toán', 'Email', 'Số điện thoại', 'Mã hồ sơ',
            'Chủ hồ sơ', 'Loại GD', 'Ngày GD', 'Số tiền (VNĐ)', 'Trạng thái', 'Mô tả',
        ], null, 'A1');
        $this->styleHeader($sheet, 'A1:L1');

        $row = 2;
        foreach ($this->history->historyQuery($filters)->get() as $payment) {
            $sheet->fromArray([
                $payment->maGD ?? '',
                $payment->soGD ?? '',
                $payment->hoTen ?? '',
                $payment->email ?? '',
                $payment->soDienThoai ?? '',
                $payment->maHSXL ?? '',
                $payment->tenChuHoSo ?? '',
                $payment->loaiGD ?? '',
                $payment->ngayGD ? Carbon::parse($payment->ngayGD)->format('d/m/Y H:i:s') : '',
                (float) ($payment->soTien ?? 0),
                $payment->trangThai ?? '',
                $payment->moTa ?? '',
            ], null, 'A'.$row++);
        }
        $sheet->getStyle('J2:J'.max(2, $row - 1))->getNumberFormat()->setFormatCode('#,##0');
        foreach ([
            'A' => 20, 'B' => 20, 'C' => 25, 'D' => 30, 'E' => 15, 'F' => 20,
            'G' => 30, 'H' => 20, 'I' => 20, 'J' => 18, 'K' => 15, 'L' => 40,
        ] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        return $this->save($spreadsheet, 'lich_su_thanh_toan_');
    }

    /** @return array{path: string, filename: string} */
    public function revenue(): array
    {
        $overview = $this->history->revenueOverview();
        $spreadsheet = new Spreadsheet;

        $summary = $spreadsheet->getActiveSheet();
        $summary->setTitle('Thống kê tổng quan');
        $summary->fromArray([
            ['Chỉ tiêu', 'Giá trị'],
            ['Tổng doanh thu', number_format($overview['totalRevenue'], 0, ',', '.').' đ'],
            ['Doanh thu hôm nay', number_format($overview['todayRevenue'], 0, ',', '.').' đ'],
            ['Doanh thu tháng này', number_format($overview['thisMonthRevenue'], 0, ',', '.').' đ'],
            ['Doanh thu năm này', number_format($overview['thisYearRevenue'], 0, ',', '.').' đ'],
        ], null, 'A1');
        $this->styleHeader($summary, 'A1:B1');
        $summary->getColumnDimension('A')->setWidth(30);
        $summary->getColumnDimension('B')->setWidth(25);

        $monthly = $spreadsheet->createSheet();
        $monthly->setTitle('Doanh thu theo tháng');
        $monthly->fromArray([['Tháng', 'Doanh thu (VNĐ)']], null, 'A1');
        $row = 2;
        foreach ($overview['monthlyData'] as $month) {
            $monthly->fromArray([[$month['month'], number_format($month['revenue'], 0, ',', '.')]], null, 'A'.$row++);
        }
        $this->styleHeader($monthly, 'A1:B1');
        $monthly->getColumnDimension('A')->setWidth(20);
        $monthly->getColumnDimension('B')->setWidth(25);

        $top = $spreadsheet->createSheet();
        $top->setTitle('Top 10 hồ sơ');
        $top->fromArray([['STT', 'Mã hồ sơ', 'Chủ hồ sơ', 'Người nộp', 'Tổng tiền (VNĐ)']], null, 'A1');
        $this->styleHeader($top, 'A1:E1');
        $row = 2;
        foreach ($overview['topHoSos'] as $index => $application) {
            $top->fromArray([[
                $index + 1,
                $application->maHSXL ?? '',
                $application->tenChuHoSo ?? '',
                $application->hoTen ?? '',
                number_format($application->total, 0, ',', '.'),
            ]], null, 'A'.$row++);
        }
        foreach (['A' => 10, 'B' => 20, 'C' => 30, 'D' => 25, 'E' => 20] as $column => $width) {
            $top->getColumnDimension($column)->setWidth($width);
        }

        return $this->save($spreadsheet, 'bao_cao_doanh_thu_');
    }

    /** @return array{path: string, filename: string} */
    private function save(Spreadsheet $spreadsheet, string $prefix): array
    {
        $path = tempnam(sys_get_temp_dir(), 'excel_');
        if ($path === false) {
            throw new \RuntimeException('Không thể tạo file Excel tạm thời.');
        }

        (new Xlsx($spreadsheet))->save($path);

        return [
            'path' => $path,
            'filename' => $prefix.date('Y-m-d_His').'.xlsx',
        ];
    }

    private function styleHeader(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
