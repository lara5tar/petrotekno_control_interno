<?php

namespace Database\Seeders;

use App\Models\Obra;
use App\Models\Personal;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ObrasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos IDs de personal disponibles para asignar como encargados
        $personalIds = Personal::pluck('id')->toArray();
        
        $obras = [
            [
                'nombre_obra' => 'Construcción de Puente Vehicular',
                'ubicacion' => 'Av. Revolución No. 450, Col. Centro',
                'estatus' => 'en_progreso',
                'avance' => 65,
                'fecha_inicio' => Carbon::now()->subDays(45),
                'fecha_fin' => Carbon::now()->addDays(30),
                'observaciones' => 'Obra de construcción de puente vehicular para mejorar la conectividad urbana',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Pavimentación Calle Principal',
                'ubicacion' => 'Calle Principal, Col. San José',
                'estatus' => 'completada',
                'avance' => 100,
                'fecha_inicio' => Carbon::now()->subDays(90),
                'fecha_fin' => Carbon::now()->subDays(10),
                'observaciones' => 'Pavimentación completa de 2.5 km de vialidad principal',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Construcción de Plaza Comercial',
                'ubicacion' => 'Blvd. Las Torres No. 1200, Col. Moderna',
                'estatus' => 'en_progreso',
                'avance' => 30,
                'fecha_inicio' => Carbon::now()->subDays(20),
                'fecha_fin' => Carbon::now()->addDays(120),
                'observaciones' => 'Plaza comercial de 3 niveles con estacionamiento subterráneo',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Remodelación Edificio Gubernamental',
                'ubicacion' => 'Plaza de Armas No. 1, Centro Histórico',
                'estatus' => 'suspendida',
                'avance' => 15,
                'fecha_inicio' => Carbon::now()->subDays(60),
                'fecha_fin' => Carbon::now()->addDays(90),
                'observaciones' => 'Suspendida temporalmente por revisión de permisos',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Instalación de Drenaje Pluvial',
                'ubicacion' => 'Col. Vista Hermosa, Sector Norte',
                'estatus' => 'planificada',
                'avance' => 0,
                'fecha_inicio' => Carbon::now()->addDays(15),
                'fecha_fin' => Carbon::now()->addDays(75),
                'observaciones' => 'Sistema de drenaje para prevención de inundaciones',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Construcción de Escuela Primaria',
                'ubicacion' => 'Calle Educación No. 89, Col. Progreso',
                'estatus' => 'en_progreso',
                'avance' => 80,
                'fecha_inicio' => Carbon::now()->subDays(120),
                'fecha_fin' => Carbon::now()->addDays(20),
                'observaciones' => 'Escuela de 12 aulas con áreas deportivas',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Ampliación de Hospital Regional',
                'ubicacion' => 'Av. Salud No. 200, Col. Médica',
                'estatus' => 'en_progreso',
                'avance' => 45,
                'fecha_inicio' => Carbon::now()->subDays(80),
                'fecha_fin' => Carbon::now()->addDays(100),
                'observaciones' => 'Ampliación de 2 pisos adicionales y nueva ala de urgencias',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Reparación de Carretera Estatal',
                'ubicacion' => 'Carretera Estatal Km 15-25',
                'estatus' => 'completada',
                'avance' => 100,
                'fecha_inicio' => Carbon::now()->subDays(50),
                'fecha_fin' => Carbon::now()->subDays(5),
                'observaciones' => 'Reparación de 10 km de carretera con nueva señalización',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Construcción de Parque Recreativo',
                'ubicacion' => 'Av. Verde No. 500, Col. Ecológica',
                'estatus' => 'en_progreso',
                'avance' => 25,
                'fecha_inicio' => Carbon::now()->subDays(30),
                'fecha_fin' => Carbon::now()->addDays(60),
                'observaciones' => 'Parque con juegos infantiles, pista para correr y áreas verdes',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Instalación de Red de Fibra Óptica',
                'ubicacion' => 'Zona Residencial Las Palmas',
                'estatus' => 'planificada',
                'avance' => 0,
                'fecha_inicio' => Carbon::now()->addDays(25),
                'fecha_fin' => Carbon::now()->addDays(85),
                'observaciones' => 'Red de fibra óptica para 500 hogares',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Remodelación de Mercado Municipal',
                'ubicacion' => 'Calle Comercio No. 45, Centro',
                'estatus' => 'en_progreso',
                'avance' => 70,
                'fecha_inicio' => Carbon::now()->subDays(40),
                'fecha_fin' => Carbon::now()->addDays(15),
                'observaciones' => 'Modernización completa con nuevas instalaciones eléctricas e hidráulicas',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Construcción de Centro Deportivo',
                'ubicacion' => 'Av. Deportes No. 300, Col. Atlética',
                'estatus' => 'suspendida',
                'avance' => 5,
                'fecha_inicio' => Carbon::now()->subDays(25),
                'fecha_fin' => Carbon::now()->addDays(150),
                'observaciones' => 'Suspendida por revisión presupuestal',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Rehabilitación de Alumbrado Público',
                'ubicacion' => 'Col. Industrial, Sector 1-5',
                'estatus' => 'completada',
                'avance' => 100,
                'fecha_inicio' => Carbon::now()->subDays(35),
                'fecha_fin' => Carbon::now()->subDays(3),
                'observaciones' => 'Cambio completo a tecnología LED en 5 sectores industriales',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Construcción de Biblioteca Municipal',
                'ubicacion' => 'Calle Cultura No. 75, Col. Intelectual',
                'estatus' => 'en_progreso',
                'avance' => 55,
                'fecha_inicio' => Carbon::now()->subDays(65),
                'fecha_fin' => Carbon::now()->addDays(45),
                'observaciones' => 'Biblioteca moderna con sala de cómputo y auditorio',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
            [
                'nombre_obra' => 'Ampliación de Red de Agua Potable',
                'ubicacion' => 'Col. Nueva Esperanza, Zona Sur',
                'estatus' => 'planificada',
                'avance' => 0,
                'fecha_inicio' => Carbon::now()->addDays(20),
                'fecha_fin' => Carbon::now()->addDays(95),
                'observaciones' => 'Extensión de red para abastecer 300 nuevas viviendas',
                'encargado_id' => $personalIds[array_rand($personalIds)] ?? null,
            ],
        ];

        foreach ($obras as $obra) {
            Obra::create($obra);
        }

        $this->command->info('Se han creado 15 obras de ejemplo exitosamente.');
    }
}