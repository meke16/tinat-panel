<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['grade_id', 'name'];

    public function grade() {
        return $this->belongsTo(Grade::class);
    }

    public function chapters() {
        return $this->hasMany(Chapter::class);
    }
}
