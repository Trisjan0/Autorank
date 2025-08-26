<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalDevelopment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'date',
        'link',
        'file_path',
        'created_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
