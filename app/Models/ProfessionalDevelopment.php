<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalDevelopment extends Model
{
    use HasFactory;

    protected $table = 'professional_developments';

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'file_path',
        'publish_date',
        'google_drive_file_id',
    ];

    protected $casts = [
        'publish_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
