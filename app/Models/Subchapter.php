<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subchapter extends Model
{
    protected $fillable = ['chapter_id', 'name'];

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }
}

