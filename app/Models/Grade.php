<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['name','order'];

    

    public function subjects() {
        return $this->hasMany(Subject::class);
    }
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    
}

