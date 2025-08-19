<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoriaPersonal;
use App\Models\Personal;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Solo seeders esenciales para la cuenta admin
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
