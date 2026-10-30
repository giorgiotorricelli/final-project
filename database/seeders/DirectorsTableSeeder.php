<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Director;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class DirectorsTableSeeder extends Seeder
{
  
    public function run(): void
    {
        $path = database_path('data/credits.json');
        $raw = File::get($path);
        $credits = json_decode($raw, true);
        $countCredits = count($credits);

        if ($countCredits === 0) {
            $this->command->error('Il file credits.json è vuoto! Scarica credits da tmbd');
            return;
        }

        $bar = $this->command->getOutput()->createProgressBar($countCredits);

        $bar->start();

        $apiKey = config('services.tmdb.api_key');
        $baseUrl = config('services.tmdb.base_url');

        $this->command->info('Inizio fetch e seeding');
        $countSuccess = 0;
        $countDuplicates = 0;

        foreach ($credits as $credit) {
            $directorContainer = $credit['director'];
            if (count($directorContainer) === 0) {
                $bar->advance();
                continue;
            }
            $director = $directorContainer[0];
            $directorId = $director['id'];
            if (Director::where('id', $directorId)->exists()) {
                $bar->advance();
                $countDuplicates++;
                continue;
            }
            $directorUrl = "$baseUrl/3/person/$directorId?api_key=$apiKey";
            $response = Http::get($directorUrl);

            if ($response->successful()) {
                $singleInfo = json_decode($response, true);
                $infoArray = [
                    "id" => $singleInfo['id'],
                    "name" => $singleInfo['name'],
                    "popularity" => $singleInfo['popularity'],
                    "profile_path" => $singleInfo['profile_path'],
                    "birthday" => $singleInfo['birthday'],
                    "deathday" => $singleInfo['deathday'],
                    "place_of_birth" => $singleInfo['place_of_birth'],
                    "biography" => $singleInfo['biography'],

                ];

                Director::create($infoArray);
                $countSuccess++;
            }
            usleep(50000);
            $bar->advance();
        }
        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("Inseriti $countSuccess directors su $countCredits credits! Duplicati esclusi: $countDuplicates. ");
    }
}
