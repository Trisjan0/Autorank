<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtensionService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'service_type',
        'date',
        'link',
        'file_path',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
