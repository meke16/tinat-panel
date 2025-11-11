<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'size',
        'duration',
        'grade_id',
        'subject_id',
        'chapter_id',
        'author',
        'url',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
