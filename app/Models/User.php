<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLES = [
        'admin', // Level 0 - Admin Utama
        'DJSN', // Level 1 - Full Access
        'Tata Usaha', // Level 2 - Manage Activities, Assignment
        'Persidangan', // Level 3 - Minutes, Attendance, Follow-up
        'Bagian Umum', // Level 4 - Documentation
        'User', // Regular User
        'Dewan', // Dewan Members
        'TA', // Tenaga Ahli (Read Only if Tagged)
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'divisi',
        'order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'DJSN', 'Tata Usaha', 'Persidangan', 'Bagian Umum']);
    }

    public function getAdminLevel(): int
    {
        switch ($this->role) {
            case 'admin': return 0;
            case 'DJSN': return 1;
            case 'Tata Usaha': return 2;
            case 'Persidangan': return 3;
            case 'Bagian Umum': return 4;
            default: return 99;
        }
    }

    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    // --- Role Helpers ---
    public function isTataUsaha()
    {
        return $this->role === 'Tata Usaha';
    }

    public function isPersidangan()
    {
        return $this->role === 'Persidangan';
    }

    public function isTA()
    {
        return $this->role === 'TA';
    }

    public function isSuperAdmin()
    {
        return in_array($this->role, ['admin', 'DJSN']);
    }

    // --- Capability Helpers ---
    
    // Create/Edit/Delete Activities
    public function canManageActivities()
    {
        return $this->isSuperAdmin() || $this->isTataUsaha();
    }

    // Upload Surat Tugas (Assignment Letter)
    public function canUploadAssignment()
    {
        return $this->isSuperAdmin() || $this->isTataUsaha();
    }

    // Summary, Minutes, Materials, Activity Completion, Attendance
    public function canManagePostActivity()
    {
        return $this->isSuperAdmin() || $this->isPersidangan();
    }

    // Follow-Ups (Tindak Lanjut)
    public function canManageFollowUp()
    {
        return $this->isSuperAdmin() || $this->isPersidangan();
    }
}
