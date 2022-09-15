<?php

namespace Database\Seeders;

use App\Models\Chirp;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ChirpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Chirp::factory()
            ->count(50)
            ->create();
    }
}
