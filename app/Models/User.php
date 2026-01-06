<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Role constants
    const ROLE_PARENT = 0;
    const ROLE_SCHOOL = 1;
    const ROLE_MASTER_ADMIN = 2;
    const ROLE_INVENTORY_ADMIN = 3;
    const ROLE_GUEST = 4;
    const ROLE_BACK_TO_SCHOOL_ADMIN = 5;
    const ROLE_MERCHANDISE_ADMIN = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'school_id',
        'has_deleted',
        'otp',
        'otp_expires_at',
        'otp_verified',
        'is_welcome_modal_seen',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
            'otp_expires_at' => 'datetime',
            'otp_verified' => 'boolean',
            'is_welcome_modal_seen' => 'boolean',
        ];
    }

    /**
     * Get the school that the user belongs to (for school role)
     */
    public function school()
    {
        return $this->belongsTo(\App\Models\Admin\Master\School::class);
    }

    public function studentProfiles()
    {
        return $this->hasMany(StudentProfile::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Check if user is a parent
     */
    public function isParent(): bool
    {
        return $this->role === self::ROLE_PARENT;
    }

    /**
     * Check if user is a school
     */
    public function isSchool(): bool
    {
        return $this->role === self::ROLE_SCHOOL;
    }

    /**
     * Check if user is a master admin
     */
    public function isMasterAdmin(): bool
    {
        return $this->role === self::ROLE_MASTER_ADMIN;
    }

    /**
     * Check if user is an inventory admin
     */
    public function isInventoryAdmin(): bool
    {
        return $this->role === self::ROLE_INVENTORY_ADMIN;
    }

    /**
     * Check if user is a Back-To-School admin
     */
    public function isBackToSchoolAdmin(): bool
    {
        return $this->role === self::ROLE_BACK_TO_SCHOOL_ADMIN;
    }

    /**
     * Check if user is a Merchandise admin
     */
    public function isMerchandiseAdmin(): bool
    {
        return $this->role === self::ROLE_MERCHANDISE_ADMIN;
    }

    /**
     * Check if user is a Store Admin (BTS or Merch)
     */
    public function isStoreAdmin(): bool
    {
        return $this->role === self::ROLE_BACK_TO_SCHOOL_ADMIN || $this->role === self::ROLE_MERCHANDISE_ADMIN;
    }

    /**
     * Get the role name for display
     */
    public function getRoleName(): string
    {
        return match($this->role) {
            self::ROLE_PARENT => 'Parent',
            self::ROLE_SCHOOL => 'School',
            self::ROLE_MASTER_ADMIN => 'Master Admin',
            self::ROLE_INVENTORY_ADMIN => 'Inventory Admin',
            self::ROLE_GUEST => 'Guest User',
            self::ROLE_BACK_TO_SCHOOL_ADMIN => 'Back to School Admin',
            self::ROLE_MERCHANDISE_ADMIN => 'Merchandise Admin',
            default => 'Unknown',
        };
    }
}
