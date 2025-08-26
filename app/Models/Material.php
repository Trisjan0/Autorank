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
        'type',
        'category',
        'link',
        'file_path',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
