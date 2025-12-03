<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'pic',
        'date_time',
        'status',
        'invitation_status',
        'location',
        'dispo_note',
        'attachment_path',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'pic' => 'array',
        'status' => 'integer',
        'invitation_status' => 'integer',
    ];

    public const STATUS_ON_SCHEDULE = 0;
    public const STATUS_RESCHEDULE = 1;
    public const STATUS_NO_DISPO = 2;
    public const STATUS_CANCELLED = 3;

    public const INV_STATUS_SENT = 0;
    public const INV_STATUS_SIGN = 1;
    public const INV_STATUS_DRAFT = 2;
}
