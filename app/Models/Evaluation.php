<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'Evaluations';
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'score',
        'link',
        'file_path',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
