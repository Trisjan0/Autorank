<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Research extends Model
{
    use HasFactory;

    protected $table = 'researches';

    /**
     * The attributes that are not mass assignable.
     *
     * Following the pattern of Instruction.php
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * This ensures that date columns from the database are
     * automatically converted to Carbon date objects.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_date' => 'date',
        'exhibition_date' => 'date',
    ];

    /**
     * Get the user that owns the research record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
