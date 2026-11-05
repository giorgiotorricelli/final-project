<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Director;

class DownloadDirectorsPic extends Command
{
    protected $signature = 'directors:pp-download';

    protected $description = 'Download directors pics from tmdb';

    public function handle()
    {
        $apiKey = config('services.tmdb.api_key');

        //$completeUrl = "https://image.tmdb.org/t/p/original/$profile_path?api_key=$apiKey";
        $dataPath = database_path('data/credits.json');

        $raw = File::get($dataPath);
        $credits = json_decode($raw, true);
        $this->info("Inizio scaricamento immagini directors per " . count($credits) . " credits!");
        $bar = $this->output->createProgressBar(count($credits));
        $bar->start();
        $successful = 0;

        foreach ($credits as $credit) {
            $director = $credit['director'];

            if (count($director) !== 0) {
                $profile_path = $director[0]['profile_path'];
                $directorId = $director[0]['id'];

                if ($profile_path !== null) {
                    $completeUrl = "https://image.tmdb.org/t/p/original/$profile_path?api_key=$apiKey";
                    $response = Http::get($completeUrl);

                    if ($response->successful()) {
                        $dbDirector = Director::where('id', $directorId)->first();

                        if ($dbDirector !== null) {
                            $local_path = "directors/" . $directorId . ".jpg";
                    
                        
                            Storage::disk('public')->put($local_path, $response->body());

                            $dbDirector->profile_path = $local_path;
                            $dbDirector->save();
                            $successful++ ;
                        }
                    }

                    

                }
            }
            $bar->advance();

            usleep(50000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Scaricate ' . $successful . ' immagini di directors per ' . count($credits) . ' credits!');
    }
}
