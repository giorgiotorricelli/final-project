<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Genre;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/genres.json'); //creo il path al file con i generi

        $rawContent = File::get($path); // recupero i dati raw

        $genresContainer = json_decode($rawContent, true);

        $genres = $genresContainer['genres'];

        foreach ($genres as $genre) {
            $newGenre = new Genre();

            $newGenre->id = $genre['id'];
            $newGenre->name = $genre['name'];

            $newGenre->save();
        }
    }
}
