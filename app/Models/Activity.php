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
        'minutes_path',
        'assignment_letter_path',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'pic' => 'array',
        'disposition_to' => 'array',
    ];

    // Council Members List
    // Council Structure
    public const COUNCIL_STRUCTURE = [
        'Ketua DJSN' => [
            'Prof. Dr. Ir. R. Nunung Nuryartono, M.Si.'
        ],
        'Komisi PME' => [
            'Muttaqien, S.S., M.P.H., A.A.K.',
            'Nikodemus Beriman Purba, S.Psi., M.H.',
            'Sudarto, S.E., M.B.A., M.Kom., Ph.D., CGEIT., CA.',
            'Robben Rico, A.Md., LLAJ., S.H., S.T., M.Si.',
            'Dr. dr. Mahesa Paranadipa Maykel, M.H., MARS.',
            'Dr.rer.pol. Syamsul Hidayat Pasaribu, S.E., M.Si.',
            'Hermansyah, S.H., AK3.'
        ],
        'Komisi Komjakum' => [
            'Drs. Paulus Agung Pambudhi, M.M.',
            'dr. H. Agus Taufiqurrohman, M.Kes., Sp.S.',
            'Kunta Wibawa Dasa Nugraha, S.E., M.A., Ph.D.',
            'Dra. Indah Anggoro Putri, M.Bus.',
            'Prof. Dr. Rudi Purwono, S.E., M.SE.',
            'Mickael Bobby Hoelman, S.E., M.Si.',
            'Royanto Purba, S.T.'
        ]
    ];

    // Internal PIC Options
    public const INTERNAL_PICS = [
        'Ketua DJSN',
        'Komisi PME',
        'Komisi Komjakum',
        'Sekretariat DJSN'
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
