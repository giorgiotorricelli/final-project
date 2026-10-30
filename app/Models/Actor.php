<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Movie;

class Actor extends Model
{
    protected $fillable = [
        'id',
        'name',
        'popularity',
        'profile_path'
    ];

    public function movies() {
        return $this->belongsToMany(Movie::class);
    }
}
