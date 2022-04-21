<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        \App\Models\Event::factory(1)->create();
        \App\Models\Artist_Event_Performance::factory(1)->create();
        \App\Models\User_Event_Follow::factory(1)->create();
        \App\Models\User_Artist_Follow::factory(1)->create();
    }
}
