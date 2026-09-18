<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoXuLy extends Model
{
    use HasFactory;

    protected $table = 'hosoxuly';

    protected $primaryKey = 'maHSXL';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'maHSXL',
        'maTTHC',
        'IDCD',
        'maForm',
        'tenChuHoSo',
        'doiTuongThucHien',
        'email',
        'soDienThoai',
        'dulieu',
        'ngayTiepNhan',
        'ngayHenTra',
        'maTrangThai',
        'ngayTra',
        'hanBoSung',
        'thongTinTra',
        'lePhi',
        'hinhThuc',
        'ngayKetThucXuLy',
        'donViXuLy',
        'ghiChu',
        'last_mail_sent_at',
        'nguoiTiepNhan',
        'nguoiDuyet',
        'ngayDuyet',
        'yKienDuyet',
        'yKienXuLy',
        'duongdanfileykien',
        'duongdanfileketqua',
        'maTrangThai_backup',
    ];

    protected $casts = [
        'dulieu' => 'array',
        'ngayTiepNhan' => 'date',
        'ngayHenTra' => 'date',
        'ngayTra' => 'date',
        'ngayKetThucXuLy' => 'date',
        'lePhi' => 'float',
        'last_mail_sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hoso) {
            if (empty($hoso->maHSXL) || $hoso->maHSXL == '0' || ! preg_match('/^HSXL_/', $hoso->maHSXL)) {
                $IDCD = $hoso->IDCD ?? 0;

                if ($IDCD == 0 && ! empty($hoso->email)) {
                    $nguoi = \Illuminate\Support\Facades\DB::table('nguoi')
                        ->where('email', $hoso->email)
                        ->first();

                    if ($nguoi) {
                        $congDan = \Illuminate\Support\Facades\DB::table('congdan')
                            ->where('IDnguoiDung', $nguoi->IDnguoiDung)
                            ->first();

                        if ($congDan) {
                            $IDCD = $congDan->IDCD;
                        } else {
                            $IDCD = \Illuminate\Support\Facades\DB::table('congdan')->insertGetId([
                                'IDnguoiDung' => $nguoi->IDnguoiDung,
                            ]);
                        }

                        $hoso->IDCD = $IDCD;
                    }
                }

                $datePart = $hoso->ngayTiepNhan
                    ? \Carbon\Carbon::parse($hoso->ngayTiepNhan)->format('Ymd')
                    : now()->format('Ymd');

                do {
                    $rand = random_int(1000, 9999);
                    $maHSXL = 'HSXL_'.$IDCD.'_'.$datePart.'_'.$rand;
                } while (self::where('maHSXL', $maHSXL)->exists());

                $hoso->maHSXL = $maHSXL;
            }
        });
    }

    public function congdan()
    {
        return $this->belongsTo(CongDan::class, 'IDCD', 'IDCD');
    }

    public function tthc()
    {
        return $this->belongsTo(TTHC::class, 'maTTHC', 'maTTHC');
    }

    public function trangThai()
    {
        return $this->belongsTo(TrangThaiHoSo::class, 'maTrangThai', 'maTrangThai');
    }

    public function receiver()
    {
        return $this->belongsTo(Nguoi::class, 'nguoiTiepNhan', 'IDnguoiDung');
    }

    public function approver()
    {
        return $this->belongsTo(Nguoi::class, 'nguoiDuyet', 'IDnguoiDung');
    }

    public function mailHistory()
    {
        return $this->hasMany(HoSoXuLyMailHistory::class, 'maHSXL', 'maHSXL');
    }

    public function files()
    {
        return $this->hasMany(TaiLieuNop::class, 'maHSXL', 'maHSXL');
    }

    public function resultFiles()
    {
        return $this->hasMany(HoSoResultFile::class, 'maHSXL', 'maHSXL');
    }

    public function opinionFiles()
    {
        return $this->hasMany(HoSoOpinionFile::class, 'maHSXL', 'maHSXL');
    }

    public function paymentHistories()
    {
        return $this->hasMany(LichSuThanhToan::class, 'maHSXL', 'maHSXL');
    }
}
