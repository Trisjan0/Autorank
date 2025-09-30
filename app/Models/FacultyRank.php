<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyRank extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank_name',
    ];

    /**
     * A faculty rank has many KRA weights.
     */
    public function kraWeights()
    {
        return $this->hasMany(FacultyRankKraWeight::class);
    }
}
