<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyRankKraWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_rank_id',
        'kra_name',
        'weight',
    ];

    /**
     * A KRA weight belongs to a faculty rank.
     */
    public function facultyRank()
    {
        return $this->belongsTo(FacultyRank::class);
    }
}
