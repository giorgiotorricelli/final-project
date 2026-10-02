<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File; //libreria per gestione file, facilita file_get_contents
use Illuminate\Support\Facades\Http; //libreria per le chiamate HTTP

class FetchMoviesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scarica:movies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaricare film da TMDB in database/data/movies.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bearerToken = config('services.tmdb.bearer_token');
        $baseUrl     = config('services.tmdb.base_url', 'https://api.themoviedb.org/3');

        if (!$bearerToken) {
            $this->error("Errore: TMDB_BEARER_TOKEN non configurato nel file .env!");
            return Command::FAILURE;
        }

        $allMovies = [];
        $allMoviesIds = []; //mi salvo solo gli id, per fare il check duplicati
        $duplicates = 0;

        $totalPages = 5; // 5 pagine x 20 film per pagina = 100 film

        $this->info("Inizio scaricamento dei film da TMDB...");
        
        // Barra di progresso nel terminale
        $bar = $this->output->createProgressBar($totalPages);
        $bar->start();

        for ($page = 1; $page <= $totalPages; $page++) {
            $response = Http::withToken($bearerToken)
                ->get("{$baseUrl}/movie/popular", [
                    'page'     => $page,
                ]);

            if ($response->failed()) {
                $this->error("\nErrore durante il recupero della pagina {$page}!");
                return Command::FAILURE;
            }

            $results = $response->json('results') ?? [];

            foreach ($results as $item) {
                //skippare i duplicati
                if(in_array($item['id'], $allMoviesIds)) { //se l'id è già presente nell'array degli id
                    $duplicates++;                        //segno che c'è stato un duplicato
                    continue;                            //skippa un ciclo
                } else {
                    $allMoviesIds[] = $item['id'];     //altrimenti salva l'id
                }

                // Mappiamo i dati nel formato pulito che vogliamo per il nostro JSON
                $allMovies[] = [
                    'tmdb_id'        => $item['id'],
                    'title'          => $item['title'],
                    'original_title' => $item['original_title'] ?? null,
                    'description'    => $item['overview'] ?? null,
                    'release_date'   => !empty($item['release_date']) ? $item['release_date'] : null,
                    'rating'         => $item['vote_average'] ?? null,
                    'poster_path'    => $item['poster_path'] ?? null,
                    'backdrop_path'  => $item['backdrop_path'] ?? null,
                    'genre_ids'      => $item['genre_ids'] ?? [], // Utile per le categorie future!
                ];
            }

            $bar->advance();
            // Una piccola pausa di 100ms per rispetto dei rate-limit delle API
            usleep(100000); 
        }

        $bar->finish();
        $this->newLine(2);

        // Definizione del percorso di destinazione
        $directoryPath = database_path('data');
        $filePath      = database_path('data/movies.json');

        // Assicuriamoci che la cartella database/data esista
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Salviamo il file JSON formattato in modo leggibile (JSON_PRETTY_PRINT)
        File::put($filePath, json_encode($allMovies, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Operazione completata con successo!");
        $this->info("Salvati " . count($allMovies) . " film in: " . $filePath);
        $duplicates !== 0 ? $this->info("Duplicati eliminati: " . $duplicates) : null;

        return Command::SUCCESS;
    }
}
