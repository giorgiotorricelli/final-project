<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Genre;
use App\Models\Actor;
use App\Models\Director;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id',
        'title',
        'slug',
        'description',
        'release_date',
        'rating',
        'poster_path',
        'backdrop_path',
    ];

    public function genres() {
        return $this->belongsToMany(Genre::class);
    }

    public function actors() {
        return $this->belongsToMany(Actor::class);
    }

    public function director() {
        return $this->belongsTo(Director::class);
    }
}
