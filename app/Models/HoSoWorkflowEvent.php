<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoSoWorkflowEvent extends Model
{
    use HasFactory;

    protected $table = 'hoso_workflow_events';

    public $timestamps = false;

    protected $fillable = [
        'maHSXL',
        'event_type',
        'actor_id',
        'actor_name',
        'actor_role',
        'from_status',
        'to_status',
        'note',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'from_status' => 'integer',
        'to_status' => 'integer',
        'created_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(HoSoXuLy::class, 'maHSXL', 'maHSXL');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Nguoi::class, 'actor_id', 'IDnguoiDung');
    }
}
