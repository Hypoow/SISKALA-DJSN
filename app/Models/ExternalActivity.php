<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_time',
        'status',
        'invitation_status',
        'location',
        'dispo_note',
        'attachment_path',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'status' => 'integer',
        'invitation_status' => 'integer',
    ];

    public const STATUS_ON_SCHEDULE = 0;
    public const STATUS_RESCHEDULE = 1;
    public const STATUS_NO_DISPO = 2;
    public const STATUS_CANCELLED = 3;

    public const INV_STATUS_PROCESS = 0;
    public const INV_STATUS_DISPO = 1;
    public const INV_STATUS_INFO = 2;
    public const INV_STATUS_ATTEND = 3;
}
