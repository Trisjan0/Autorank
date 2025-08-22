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
        ];
    }

    /**
     * Get the user's credentials.
     */
    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class);
    }

    // This part tells the user model about its relationships with evaluations and materials
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Get the performance metrics for the user.
     */
    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(performanceMetric::class);
    }

    /**
     * Get the promotion applications for the user.
     */
    public function promotionApplications(): HasMany
    {
        return $this->hasMany(PromotionApplication::class);
    }

    public function assignDefaultRoleByEmail(): void
    {
        // Define email lists here.
        // b4 deployment store on either a config or db
        $superAdminEmails = [
            'autorank.team@gmail.com',
        ];

        $adminEmails = [
            '2020103851@pampangastateu.edu.ph',
            '2022310186@pampangastateu.edu.ph'
        ];

        if (in_array($this->email, $superAdminEmails)) {
            $assignedRole = 'super_admin';
        } elseif (in_array($this->email, $adminEmails)) {
            $assignedRole = 'admin';
        } else {
            $assignedRole = 'user';
        }

        $this->syncRoles([$assignedRole]);

        $this->role_assigned_at = Carbon::now();
        $this->role_assigned_by = 'System';
        $this->save();
    }
}
