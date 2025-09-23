<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'materials';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'type',
        'date',
        'score',
        'google_drive_file_id',
        'filename',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Get the user that owns the material.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
