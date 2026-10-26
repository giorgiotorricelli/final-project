<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Movie;

class Genre extends Model
{
    protected $fillable = [
        'id',
        'name'
    ];

    public function Movies() {
        return $this->belongsToMany(Movie::class);
    }
}
