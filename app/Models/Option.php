<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'text',
        'order_index',
    ];

    protected $casts = [
        'text' => 'array', // If storing JSON for rich editor
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}