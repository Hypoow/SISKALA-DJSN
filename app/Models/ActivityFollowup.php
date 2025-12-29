<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'topic',
        'instruction',
        'pic',
        'progress_notes',
        'percentage',
        'status',
        'deadline',
        'notes',
        'completion_date',
    ];

    protected $casts = [
        'deadline' => 'date',
        'completion_date' => 'datetime',
    ];

    // Status Constants
    public const STATUS_PENDING = 0;
    public const STATUS_ON_PROGRESS = 1;
    public const STATUS_DONE = 2;
    public const STATUS_DROPPED = 3;

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ON_PROGRESS => 'On Progress',
        self::STATUS_DONE => 'Selesai Tindak Lanjut',
        self::STATUS_DROPPED => 'Tidak Di Tindak Lanjut',
    ];

    public const STATUS_COLORS = [
        self::STATUS_PENDING => 'secondary',
        self::STATUS_ON_PROGRESS => 'warning',
        self::STATUS_DONE => 'success',
        self::STATUS_DROPPED => 'danger',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
