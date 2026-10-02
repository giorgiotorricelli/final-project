<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http; //importo la facciata per utilizzare la libreria Http e fare chiamate API

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:popular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command per testare';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //recupero l'api key e l'url base di tmdb già registrati in config/services.php
        $api_key = config('services.tmdb.api_key');
        $base_url = config('services.tmdb.base_url', 'https://api.themoviedb.org/3');

        //stampa nel terminale la scritta
        $this->info("Chiamata in corso a TMDB...");

        //facciamo una chiamata http GET a tmdb, in particolare all'endpoint dei popular movies
        $response = Http::get("{$base_url}/movie/popular", [
            'api_key'  => $api_key,
            'page'     => 1,       // Chiediamo la pagina 1
        ]);

        //se la chiamata non va a buon fine
        if ($response->failed()) {
            $this->error("Si è verificato un errore durante la chiamata all'API!");
            $this->line($response);
            return;
        }

        //trasformiamo la risposta JSON ricevuta in un array PHP
        $data = $response->json();

        $filmTrovati = $data['results'];

        $this->info("Trovati " . count($filmTrovati) . " film nella pagina 1!");
        $this->newLine();

        foreach ($filmTrovati as $film) {
            $this->line("Titolo: " . $film['title']);
        }
    }
}
