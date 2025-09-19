<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtensionService extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'extension_services';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'title',
        'service_type',
        'file_path',
        'date',
        'google_drive_file_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Get the user that owns the extension service.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
