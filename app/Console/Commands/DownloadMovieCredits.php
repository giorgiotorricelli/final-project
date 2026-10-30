<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DownloadMovieCredits extends Command
{
    protected $signature = 'movie:download-credits';
    protected $description = 'Download a bunch of movie credits from TMDB';

   
    public function handle()
    {
        $bearerToken = config('services.tmdb.api_key');
        $baseUrl     = config('services.tmdb.base_url', 'https://api.themoviedb.org');
        
        $creditsPath = database_path('data/credits.json');
        $moviePath = database_path('data/movies.json');
        $raw = File::get($moviePath);
        $movies = json_decode($raw, true);

        $credits = []; //questo fa da container, poi lo metteremo in credits.json

        $this->info("Inizio scaricamento dei credits da TMDB...");
        
        // Barra di progresso nel terminale
        $bar = $this->output->createProgressBar(count($movies));
        $bar->start();

        foreach ($movies as $movie) {
            $creditUrl = "$baseUrl/3/movie/{$movie['tmdb_id']}/credits?api_key=$bearerToken";

            $response = Http::get($creditUrl);

            if ($response->failed()) {
                $this->error("\nErrore durante il recupero della pagina!");
                return Command::FAILURE;
            }
            
            $credit = $response->json() ?? [];

            $movie_id = $credit['id'];
            // qua lo converto in collezione per utilizzare la sortBy delle collections
            $sortedCast = collect($credit['cast'])
                                    ->sortByDesc('popularity') // ordino per popularity
                                    ->take(5); // ne prendo solo 5

            $cast = $sortedCast->values()->all();

            $collDirector = collect($credit['crew'])->where('job', '=', 'Director');
            $director = $collDirector->values()->all();

            $skinnyCredit = [
                "movie_id" => $movie_id,
                "cast" => $cast,
                "director" => $director
            ];

            $credits[] = $skinnyCredit;
            $bar->advance();
            // Una piccola pausa di 100ms per rispetto dei rate-limit delle API
            usleep(100000);

            
        }

        $encodedCredits = json_encode($credits, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        File::put($creditsPath, $encodedCredits);

        $bar->finish();
        $this->newLine(2);
        $count = count($credits);

        $this->info("Scaricamento completato! Inseriti $count credits in 'data/credits.json'.");

        return Command::SUCCESS;

    }
}
