<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PersonalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Personal::factory(10)->create();
    }
}
