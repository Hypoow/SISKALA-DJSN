<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'code',
        'structure_group',
        'access_profile',
        'order',
        'receives_disposition',
        'disposition_group_label',
        'report_target_label',
    ];

    protected $casts = [
        'receives_disposition' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
