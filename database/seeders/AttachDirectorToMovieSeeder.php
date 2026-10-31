<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Movie;

class AttachDirectorToMovieSeeder extends Seeder
{
   
    public function run(): void
    {
        $path = database_path('data/credits.json');
        $raw = File::get($path);
        $credits = json_decode($raw, true);
        $creditsCollected = collect($credits);
        
        $movies = Movie::all();

        foreach ($movies as $movie) {
            // Trova subito il film nel JSON senza rischiare errori di indici
            $credit = $creditsCollected->firstWhere('movie_id', $movie->tmdb_id);

            if (!$credit) {
                continue; // Salta il giro se il film non c'è
            }

            // Prende il primo regista in modo sicuro (anche se l'array è vuoto)
            $firstDirector = collect($credit['director'] ?? [])->first();

            // Se esiste un regista con un ID valido, lo assegna al film
            if ($firstDirector && isset($firstDirector['id'])) {
                $movie->director_id = $firstDirector['id'];
                $movie->save();
            }

        }
    }
}
