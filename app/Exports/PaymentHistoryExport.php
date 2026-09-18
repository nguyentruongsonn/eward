<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentHistoryExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('lichsuthanhtoan')
            ->leftJoin('congdan', 'lichsuthanhtoan.IDCD', '=', 'congdan.IDCD')
            ->leftJoin('nguoi', 'congdan.IDnguoiDung', '=', 'nguoi.IDnguoiDung')
            ->leftJoin('hosoxuly', 'lichsuthanhtoan.maHSXL', '=', 'hosoxuly.maHSXL')
            ->select(
                'lichsuthanhtoan.*',
                'nguoi.hoTen',
                'nguoi.email',
                'nguoi.soDienThoai',
                'hosoxuly.tenChuHoSo'
            );

        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('lichsuthanhtoan.maGD', 'LIKE', "%{$search}%")
                    ->orWhere('nguoi.hoTen', 'LIKE', "%{$search}%")
                    ->orWhere('nguoi.email', 'LIKE', "%{$search}%")
                    ->orWhere('hosoxuly.tenChuHoSo', 'LIKE', "%{$search}%");
            });
        }

        if (isset($this->filters['loaiGD']) && $this->filters['loaiGD']) {
            $query->where('lichsuthanhtoan.loaiGD', $this->filters['loaiGD']);
        }

        if (isset($this->filters['trangThai']) && $this->filters['trangThai']) {
            $query->where('lichsuthanhtoan.trangThai', $this->filters['trangThai']);
        }

        if (isset($this->filters['from_date']) && $this->filters['from_date']) {
            $query->whereDate('lichsuthanhtoan.ngayGD', '>=', $this->filters['from_date']);
        }

        if (isset($this->filters['to_date']) && $this->filters['to_date']) {
            $query->whereDate('lichsuthanhtoan.ngayGD', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('lichsuthanhtoan.ngayGD', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Mã GD',
            'Số GD',
            'Người thanh toán',
            'Email',
            'Số điện thoại',
            'Mã hồ sơ',
            'Chủ hồ sơ',
            'Loại GD',
            'Ngày GD',
            'Số tiền (VNĐ)',
            'Trạng thái',
            'Mô tả',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->maGD ?? '',
            $payment->soGD ?? '',
            $payment->hoTen ?? '',
            $payment->email ?? '',
            $payment->soDienThoai ?? '',
            $payment->maHSXL ?? '',
            $payment->tenChuHoSo ?? '',
            $payment->loaiGD ?? '',
            $payment->ngayGD ? \Carbon\Carbon::parse($payment->ngayGD)->format('d/m/Y H:i:s') : '',
            number_format($payment->soTien ?? 0, 0, ',', '.'),
            $payment->trangThai ?? '',
            $payment->moTa ?? '',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 25,
            'D' => 30,
            'E' => 15,
            'F' => 20,
            'G' => 30,
            'H' => 20,
            'I' => 20,
            'J' => 18,
            'K' => 15,
            'L' => 40,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
