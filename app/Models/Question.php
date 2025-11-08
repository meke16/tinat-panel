<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = ['subchapter_id', 'question_text', 'explanation', 'answerIndex','tags'];

    public function subchapter(): BelongsTo {
        return $this->belongsTo(Subchapter::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('order_index');
    }
}
