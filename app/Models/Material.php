<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',       // pdf, docx, pptx, etc.
        'size',       // in bytes
        'grade_id',
        'subject_id',
        'chapter_id',
        'author',
        'url',        // file path
    ];

    // Relationships
    public function grade() {
        return $this->belongsTo(Grade::class);
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }
}
