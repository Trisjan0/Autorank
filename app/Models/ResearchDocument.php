<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchDocument extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'research_documents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'category',
        'publish_date',
        'file_path',
        'google_drive_file_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'publish_date' => 'datetime',
    ];

    /**
     * Get the user that owns the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
