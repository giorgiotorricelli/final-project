<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Movie;

class MoviesAttachGenresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/movies.json');

        $rawContent = File::get($path);

        $movies = json_decode($rawContent, true); // prendo la lista dei film

        foreach ($movies as $movie) {
            $movieGenres = $movie['genre_ids']; // creo un array temporaneo con gli id presi da tmdb
            $dbMovie = Movie::where('title', $movie['title'])->first();

            $dbMovie->genres()->sync($movieGenres);
        }
    }
}
