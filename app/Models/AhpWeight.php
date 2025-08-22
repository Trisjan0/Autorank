<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpWeight extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'criterion_id',
        'weight',
    ];

    /**
     * Get the criterion that owns the weight.
     */
    public function criterion()
    {
        return $this->belongsTo(AhpCriterion::class, 'criterion_id');
    }
}
