<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;
use Illuminate\Support\Facades\File;

class ActorMovieTableSeeder extends Seeder
{
    
    public function run(): void
    {
        $path = database_path('data/credits.json');
        $raw = File::get($path);
        
        $credits = json_decode($raw, true);

        foreach ($credits as $credit) {
            $movieId = $credit['movie_id'];
            $cast = $credit['cast'];
            $actors = [];
            foreach ($cast as $actor) {
                $actorId = $actor['id'];
                $character = $actor['character'];
                // qua andrò a creare un array con key actorId e value un array ['character' => $character]
                // in modo da fare il sync e non l'attach
                $actors[$actorId] = ["character" => $character];
            }
            $movie = Movie::where('tmdb_id', $movieId)->first();
            $movie->actors()->sync($actors);
        }
    }
}
