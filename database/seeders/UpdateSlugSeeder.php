<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;
use Illuminate\Support\Str;

class UpdateSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Movie::chunkById(100, function($movies){
            foreach ($movies as $movie) {
                $combined = $movie->title . ' ' . $movie->release_date;
                $newSlug = Str::slug($combined); //restituisce uno slug-pulito
                $movie->slug = $newSlug;
                $movie->save();
            }
        });
    }
}
