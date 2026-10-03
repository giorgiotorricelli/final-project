<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadMovieMediaCommand extends Command
{
    protected $signature = 'film:download-media';
    protected $description = 'Scarica poster e backdrop da TMDB e li salva nello storage locale';

    public function handle()
    {
        // URL base ufficiale di TMDB per le immagini
        // w500 per i poster (buona risoluzione senza occupare troppi MB)
        // w1280 per i backdrop
        $tmdbImageBaseUrl = 'https://image.tmdb.org/t/p/';

        $movies = Movie::all();

        if ($movies->isEmpty()) {
            $this->warn("Nessun film trovato nel database! Popola prima il DB con il seeder.");
            return Command::FAILURE;
        }

        $this->info("Inizio scaricamento immagini per " . $movies->count() . " film...");

        $bar = $this->output->createProgressBar($movies->count());
        $bar->start();

        foreach ($movies as $movie) {
            // 1. Scarica il Poster
            if ($movie->poster_path && !str_contains($movie->poster_path, 'posters/')) {
                $posterUrl = $tmdbImageBaseUrl . 'w500' . $movie->poster_path;
                $response = Http::get($posterUrl);

                if ($response->successful()) {
                    // Nome del file locale: e.g. "posters/12345.jpg" usando il tmdb_id
                    $localPosterPath = 'posters/' . $movie->tmdb_id . '.jpg';
                    
                    // Salviamo nel disk 'public' (storage/app/public/posters/...)
                    Storage::disk('public')->put($localPosterPath, $response->body());

                    // Aggiorniamo il percorso nel DB per puntare al file locale
                    $movie->poster_path = $localPosterPath;
                }
            }

            // 2. Scarica il Backdrop (Sfondo)
            if ($movie->backdrop_path && !str_contains($movie->backdrop_path, 'backdrops/')) {
                $backdropUrl = $tmdbImageBaseUrl . 'w1280' . $movie->backdrop_path;
                $response = Http::get($backdropUrl);

                if ($response->successful()) {
                    $localBackdropPath = 'backdrops/' . $movie->tmdb_id . '.jpg';

                    Storage::disk('public')->put($localBackdropPath, $response->body());

                    $movie->backdrop_path = $localBackdropPath;
                }
            }

            // Salviamo le modifiche sul record del film
            $movie->save();

            $bar->advance();
            // Pausa di 50ms per evitare di saturare la banda
            usleep(50000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Scaricamento completato! Tutte le immagini sono salvate in storage/app/public.");

        return Command::SUCCESS;
    }
}