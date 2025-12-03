<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'date_time',
        'pic',
        'status',
        'invitation_status',
        'invitation_type',
        'location_type',
        'location',
        'meeting_link',
        'dispo_note',
        'disposition_to',
        'dresscode',
        'attachment_path',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'pic' => 'array',
        'disposition_to' => 'array',
    ];

    // Council Members List
    public const COUNCIL_MEMBERS = [
        'Prof. Dr. Ir. R. Nunung Nuryartono, M. Si',
        'Sudarto, S.E., M.B.A., M.KOM., Ph.D., CGEIT., CA',
        'Kunta Wibawa Dasa Nugraha, S.E., M.A., Ph.D',
        'Dra. Indah Anggoro Putri, M. Bus',
        'Robben Rico, AMd., LLAJ., S.H., S.T., M.Si',
        'dr. Agus Taufiqurrohman M.Kes., Sp.S',
        'Muttaqien, MPH., AAK',
        'Prof. Dr. Rudi Purwono',
        'dr. Mahesa Paranadipa Maykel MH, MARS',
        'Dr.rer.pol. Syamsul Hidayat Pasaribu',
        'Mickael Bobby Hoelman, SE., M.Si., CGOP',
        'Nikodemus Beriman Purba, S.Psi., M.H',
        'Drs. Paulus Agung Pambudhi, M.M',
        'Royanto Purba, ST.',
        'Hermansyah, S.H., AK3',
    ];

    // Constants for Status
    public const STATUS_ON_SCHEDULE = 0;
    public const STATUS_RESCHEDULE = 1;
    public const STATUS_NO_DISPO = 2;
    public const STATUS_CANCELLED = 3;

    // Constants for Invitation Status (External)
    public const INV_EXT_PROCESS = 0;
    public const INV_EXT_DISPO = 1;
    public const INV_EXT_INFO = 2;
    public const INV_EXT_ATTEND = 3;

    // Constants for Invitation Status (Internal)
    public const INV_INT_SENT = 0;
    public const INV_INT_SIGN = 1;
    public const INV_INT_DRAFT = 2;
}
