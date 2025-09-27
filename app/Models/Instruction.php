<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'criterion',
        'title',
        'category',
        'type',
        'role',
        'publication_date',
        'service_type',
        'student_or_competition',
        'completion_date',
        'level',
        'score',
        'google_drive_file_id',
        'proof_filename',
    ];
}
