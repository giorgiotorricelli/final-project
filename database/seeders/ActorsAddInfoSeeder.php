<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Actor;
use Illuminate\Support\Facades\Http;

class ActorsAddInfoSeeder extends Seeder
{

    public function run(): void
    {
        $actors = Actor::all();
        $countActors = count($actors);

        if (count($actors) === 0) {
            $this->command->error('La tabella actors è vuota! Prima popolala');
            return;
        }

        $apiKey = config('services.tmdb.api_key');
        $baseUrl = config('services.tmdb.base_url');

        $this->command->info('Inizio fetch e seeding');
        $countSuccess = 0;

        foreach ($actors as $actor) {
            $actorId = $actor->id;
            $actorUrl = "$baseUrl/3/person/$actorId?api_key=$apiKey";
            $response = Http::get($actorUrl);

            if ($response->successful()) {
                $singleInfo = json_decode($response, true);
                $actor->biography = $singleInfo['biography'];
                $actor->birthday = $singleInfo['birthday'];
                $actor->deathday = $singleInfo['deathday'];
                $actor->place_of_birth = $singleInfo['place_of_birth'];
                $actor->save();
                $countSuccess++;
            }
        }

        $this->command->info("Aggiornati $countSuccess movies su $countActors");

        

    }
}
