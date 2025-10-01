<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'instructor_number',
        'rank',
        'phone_number',
        'role_assigned_at',
        'role_assigned_by',
        'google_token',
        'google_refresh_token',
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
            'role_assigned_at' => 'datetime',
            'rank_assigned_at' => 'datetime',
        ];
    }

    /**
     * Default model-level attributes
     */
    protected $attributes = [
        'faculty_rank' => 'Unset',
        'rank_assigned_by' => 'N/A',
    ];

    // --- START: RELATIONSHIPS ---

    /**
     * Get all of the applications for the User.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get all of the instruction submissions for the User.
     */
    public function instructions(): HasMany
    {
        return $this->hasMany(Instruction::class);
    }

    /**
     * Get all of the research submissions for the User.
     */
    public function researches(): HasMany
    {
        return $this->hasMany(Research::class);
    }

    /**
     * Get all of the extension submissions for the User.
     */
    public function extensions(): HasMany
    {
        return $this->hasMany(Extension::class);
    }

    /**
     * Get all of the professional development submissions for the User.
     */
    public function professionalDevelopments(): HasMany
    {
        return $this->hasMany(ProfessionalDevelopment::class);
    }

    // --- END: RELATIONSHIPS ---

    public function assignDefaultRoleByEmail(): void
    {
        $superAdminEmails = config('roles.super_admins', []);
        $adminEmails = config('roles.admins', []);
        $evaluatorEmails = config('roles.evaluators', []);

        if (in_array($this->email, $superAdminEmails)) {
            $assignedRole = 'super_admin';
        } elseif (in_array($this->email, $adminEmails)) {
            $assignedRole = 'admin';
        } elseif (in_array($this->email, $evaluatorEmails)) {
            $assignedRole = 'evaluator';
        } else {
            $assignedRole = 'user';
        }

        $this->syncRoles([$assignedRole]);

        $this->role_assigned_at = Carbon::now();
        $this->role_assigned_by = 'System';
        $this->save();
    }
}
