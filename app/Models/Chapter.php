<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = ['subject_id', 'name'];

    public function subject() {
        return $this->belongsTo(Subject::class);
    }
    public function grade() {
        return $this->subject->grade();
    }
    public function subchapters() {
        return $this->hasMany(Subchapter::class);
    }
    public function materials() {
        return $this->hasMany(Material::class);
    }
}

