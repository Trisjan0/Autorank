<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ahp_score',
        'eligible_rank',
        'status',
    ];

    /**
     * Get the user that submitted the application.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
