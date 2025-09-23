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
        'filename',
        'sub_cat1_score',
        'sub_cat2_score',
        'sub_cat3_score',
        'sub_cat4_score',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'publish_date' => 'datetime',
        'sub_cat1_score' => 'decimal:2',
        'sub_cat2_score' => 'decimal:2',
        'sub_cat3_score' => 'decimal:2',
        'sub_cat4_score' => 'decimal:2',
    ];

    /**
     * Accessor for a unified score field.
     *
     * Returns whichever subcategory score is populated.
     */
    public function getScoreAttribute()
    {
        return $this->sub_cat1_score
            ?? $this->sub_cat2_score
            ?? $this->sub_cat3_score
            ?? $this->sub_cat4_score;
    }

    /**
     * Get the user that owns the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
