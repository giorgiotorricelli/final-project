<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;

class AddGenresColorSeeder extends Seeder
{
    
    public function run(): void
    {
        $palette = [
            '#e3aa71',
            '#d75841',
            '#a61e49',
            '#450c3d',
            '#913362',
            '#de5d70',
            '#eb9494',
            '#f2c6b8',
            '#65aacf',
            '#335f9e',
            '#171559',
            '#150a2b',
            '#2b7873',
            '#2b9e62',
            '#67d95f',
            '#d6d169',
            '#e6e3d8',
            '#ab9682',
            '#544540',
            '#1f1718',
        ];

        $genres = Genre::all();
        $count = 0;

        foreach ($genres as $genre){
            $genre->color = $palette[$count];
            $genre->save();
            $count++;
        }
    }
}
