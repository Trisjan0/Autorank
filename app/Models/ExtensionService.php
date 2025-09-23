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
        'category',
        'type',
        'file_path',
        'google_drive_file_id',
        'filename',
        'sub_cat1_score', // Service to the Institution
        'sub_cat2_score', // Service to the Community
        'sub_cat3_score', // Extension Involvement
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'datetime',
        'sub_cat1_score' => 'decimal:2',
        'sub_cat2_score' => 'decimal:2',
        'sub_cat3_score' => 'decimal:2',
    ];

    /**
     * Get the user that owns the extension service.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
