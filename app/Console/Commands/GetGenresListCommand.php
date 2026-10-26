<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File; //libreria per gestione file, facilita file_get_contents
use Illuminate\Support\Facades\Http; //libreria per le chiamate HTTP

class GetGenresListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ottieni la lista dei generi da tmdb';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bearerToken = config('services.tmdb.bearer_token');
        $baseUrl = config('services.tmdb.base_url');

        $response = Http::withToken($bearerToken)->get("{$baseUrl}/3/genre/movie/list");

        if ($response->failed()) {
            $this->error("\nErrore durante il recupero della pagina!");
                return Command::FAILURE;
        }

        $results = $response->json() ?? [];

        // Definizione del percorso di destinazione
        $directoryPath = database_path('data');
        $filePath      = database_path('data/genres.json');

        // Assicuriamoci che la cartella database/data esista
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Salviamo il file JSON formattato in modo leggibile (JSON_PRETTY_PRINT)
        File::put($filePath, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
