<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceMetric extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'value',
    ];

    /**
     * Get the user that owns the performance metric.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
