<?php

namespace Database\Seeders;

use App\Models\CategoriaPersonal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaPersonalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre_categoria' => 'Administrador'],
            ['nombre_categoria' => 'Supervisor'],
            ['nombre_categoria' => 'Operador'],
            ['nombre_categoria' => 'Técnico'],
            ['nombre_categoria' => 'Mecánico'],
            ['nombre_categoria' => 'Jefe de Obra'],
        ];

        foreach ($categorias as $categoria) {
            CategoriaPersonal::create($categoria);
        }
    }
}
