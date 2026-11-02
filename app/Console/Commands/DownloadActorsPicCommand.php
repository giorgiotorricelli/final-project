<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Actor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadActorsPicCommand extends Command
{
    protected $signature = 'actors:pp-download';

    protected $description = 'Download actors profile pic from tmdb';

    public function handle()
    {
        $actors = Actor::all();
        $downloaded = 0;

        if ($actors->isEmpty()) {
            $this->warn("Nessun attore trovato nel database! Popola prima il DB con il seeder.");
            return Command::FAILURE;
        }

        $this->info("Inizio scaricamento immagini per " . $actors->count() . " attori...");

        $bar = $this->output->createProgressBar($actors->count());
        $bar->start();

        $api_key = config('services.tmdb.api_key');

        foreach ($actors as $actor) {
            $actorId = $actor->id;
            $detailsPath = "https://api.themoviedb.org/3/person/$actorId?api_key=$api_key";
            $detailResponse = Http::get($detailsPath);

            if ($detailResponse->successful()) {
                $profile_path = $detailResponse['profile_path'];
            } else {
                continue;
            }
            $baseUrl = "https://image.tmdb.org/t/p/original$profile_path?api_key=$api_key";
            $response = Http::get($baseUrl);

            if ($response->successful()) {
                $local_img_path = "actors/" . $actor->id . ".jpg";
                Storage::disk('public')->put($local_img_path, $response->body());
                $actor->profile_path = $local_img_path;
                $actor->save();
            }

            $bar->advance();
            // Pausa di 50ms per evitare di saturare la banda
            usleep(50000);
            $downloaded++;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Scaricate $downloaded immagini per " . $actors->count() . " attori!");
        return Command::SUCCESS;
    }
}
