<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityMaterial extends Model
{
    protected $fillable = ['activity_id', 'title', 'file_path'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
