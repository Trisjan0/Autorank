<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'applications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'position_id',
        'applicant_name',
        'applicant_current_rank',
        'status',
        'kra1_score',
        'kra2_score',
        'kra3_score',
        'kra4_score',
        'final_score',
        'remarks',
    ];

    /**
     * Get the user who owns the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the position for which the application was submitted.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
