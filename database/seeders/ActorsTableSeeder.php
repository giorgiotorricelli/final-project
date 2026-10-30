<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Actor;
use Illuminate\Support\Facades\File;

class ActorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $actorIds = Actor::pluck('id')->values()->all();
        // dd(count($actorIds));
        $path = database_path('data/credits.json');
        $raw = File::get($path);

        $credits = json_decode($raw, true);


        foreach ($credits as $credit) {
            $actorIds = Actor::pluck('id')->values()->all();
            $cast = $credit['cast'];
            foreach ($cast as $castActor) {
                if (!in_array($castActor['id'], $actorIds)) {
                    $newActor = new Actor();
                    $newActor->id = $castActor['id'];
                    $newActor->name = $castActor['name'];
                    $newActor->popularity = $castActor['popularity'];
                    $newActor->profile_path = $castActor['profile_path'];
                    $newActor->save();
                }
            }
            
        }
    }
}
