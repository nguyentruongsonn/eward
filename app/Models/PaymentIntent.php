<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentIntent extends Model
{
    use HasFactory;

    protected $table = 'payment_intents';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', 'IDCD', 'maHSXL', 'provider', 'amount', 'currency', 'status',
        'provider_transaction_id', 'metadata', 'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'status' => PaymentStatus::class,
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $intent): void {
            $intent->id ??= (string) Str::uuid();
            $intent->currency ??= 'VND';
            $intent->status ??= PaymentStatus::Pending;
        });
    }

    public function application()
    {
        return $this->belongsTo(HoSoXuLy::class, 'maHSXL', 'maHSXL');
    }

    public function citizen()
    {
        return $this->belongsTo(CongDan::class, 'IDCD', 'IDCD');
    }
}
