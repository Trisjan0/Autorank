<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpCriterion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_id',
    ];

    /**
     * Get the parent criterion.
     */
    public function parent()
    {
        return $this->belongsTo(AhpCriterion::class, 'parent_id');
    }

    /**
     * Get the child criteria.
     */
    public function children()
    {
        return $this->hasMany(AhpCriterion::class, 'parent_id');
    }

    /**
     * Get the weight associated with the criterion.
     */
    public function weight()
    {
        return $this->hasOne(AhpWeight::class, 'criterion_id');
    }
}
