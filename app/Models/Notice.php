<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $table = 'notices';

    const CREATED_AT = 'create_at';

    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'title',
        'content',
        'create_at',
        'update_at',
    ];
}
