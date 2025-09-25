<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    use HasFactory;

    protected $guarded = []; // Or use $fillable

    /**
     * An accessor to automatically calculate the 'total_score' attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function totalScore(): Attribute
    {
        return Attribute::make(
            get: fn() => (isset($this->attributes['student_score']) && isset($this->attributes['supervisor_score']))
                ? number_format(($this->attributes['student_score'] + $this->attributes['supervisor_score']) / 2, 2)
                : null,
        );
    }
}
