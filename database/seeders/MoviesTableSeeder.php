<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie; //import il model
use Illuminate\Support\Facades\File; //importo la facade File
use Illuminate\Support\Carbon; //importo carbon per il parsing (string to date)

function slugify($title, $release) {
    $sTitle = strtolower(str_replace(" ", "_", $title)); 
    $slug = $sTitle . $release;

    return $slug;
}

class MoviesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path_absolute = database_path('data/movies.json');
        //D:\xampp\htdocs\Laravel\final-project\database\data/movies.json

        if(File::exists($path_absolute)) {
            $rawContent = File::get($path_absolute);

            $movies = json_decode($rawContent, true);
            // si può ottenere lo stesso risultato con il metodo statico File::json($rawContent)
        }

        foreach ($movies as $movie) {
            $newMovie = new Movie();

            $newMovie->tmdb_id = $movie['tmdb_id'];
            $newMovie->title = $movie['title'];
            $newMovie->slug = slugify($movie['title'], $movie['release_date']);
            $newMovie->description = $movie['description'];
            $newMovie->release_date = Carbon::parse($movie['release_date']);
            $newMovie->rating = $movie['rating'];
            $newMovie->poster_path = $movie['poster_path'];
            $newMovie->backdrop_path = $movie['backdrop_path'];

            $newMovie->save();
        }
    }
}
