<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $table = 'Materials';
    protected $fillable = [
        'id',
        'title',
        'Type',
        'Category',
        'link',
        'file_path',
        'created_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
