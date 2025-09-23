<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'evaluations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'type',
        'sub_cat1_score',
        'sub_cat2_score',
        'publish_date',
        'google_drive_file_id',
        'filename'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'publish_date' => 'datetime',
    ];

    /**
     * Get the user that owns the evaluation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
