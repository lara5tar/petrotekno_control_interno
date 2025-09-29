<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obra;
use Carbon\Carbon;

class ObrasTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obras = [
            [
                'nombre_obra' => 'Construcción de Puente Vehicular Norte',
                'ubicacion' => 'Av. Principal Norte, Km 15',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => Carbon::now()->addDays(15),
                'fecha_fin' => Carbon::now()->addMonths(8),
                'observaciones' => 'Proyecto de infraestructura vial para mejorar conectividad urbana'
            ],
            [
                'nombre_obra' => 'Ampliación de Planta Industrial Sector B',
                'ubicacion' => 'Zona Industrial Las Flores, Lote 42',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 35,
                'fecha_inicio' => Carbon::now()->subDays(45),
                'fecha_fin' => Carbon::now()->addMonths(4),
                'observaciones' => 'Expansión de capacidad productiva con nuevas líneas de manufactura'
            ],
            [
                'nombre_obra' => 'Remodelación Centro Comercial Plaza Sur',
                'ubicacion' => 'Boulevard Sur 1250, Centro Comercial Plaza Sur',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 68,
                'fecha_inicio' => Carbon::now()->subDays(120),
                'fecha_fin' => Carbon::now()->addDays(60),
                'observaciones' => 'Modernización de espacios comerciales y mejora de accesibilidad'
            ],
            [
                'nombre_obra' => 'Construcción Complejo Habitacional Los Pinos',
                'ubicacion' => 'Fraccionamiento Los Pinos, Etapa 3',
                'estatus' => Obra::ESTATUS_SUSPENDIDA,
                'avance' => 25,
                'fecha_inicio' => Carbon::now()->subDays(90),
                'fecha_fin' => Carbon::now()->addMonths(10),
                'observaciones' => 'Proyecto suspendido temporalmente por revisión de permisos municipales'
            ],
            [
                'nombre_obra' => 'Pavimentación Carretera Estatal KM 25-40',
                'ubicacion' => 'Carretera Estatal 105, Tramo KM 25-40',
                'estatus' => Obra::ESTATUS_COMPLETADA,
                'avance' => 100,
                'fecha_inicio' => Carbon::now()->subDays(180),
                'fecha_fin' => Carbon::now()->subDays(15),
                'observaciones' => 'Proyecto completado exitosamente con mejoras en señalización vial'
            ],
            [
                'nombre_obra' => 'Instalación Sistema de Riego Agrícola',
                'ubicacion' => 'Ejido San Miguel, Parcelas 15-30',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => Carbon::now()->addDays(30),
                'fecha_fin' => Carbon::now()->addMonths(3),
                'observaciones' => 'Sistema de riego por goteo para optimización del uso de agua'
            ],
            [
                'nombre_obra' => 'Construcción Bodega Industrial Zona Este',
                'ubicacion' => 'Parque Industrial Zona Este, Nave 8',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 42,
                'fecha_inicio' => Carbon::now()->subDays(60),
                'fecha_fin' => Carbon::now()->addMonths(2),
                'observaciones' => 'Bodega de almacenamiento con sistemas automatizados de inventario'
            ],
            [
                'nombre_obra' => 'Rehabilitación Edificio Corporativo Centro',
                'ubicacion' => 'Av. Juárez 450, Centro Histórico',
                'estatus' => Obra::ESTATUS_CANCELADA,
                'avance' => 15,
                'fecha_inicio' => Carbon::now()->subDays(30),
                'fecha_fin' => Carbon::now()->addMonths(6),
                'observaciones' => 'Proyecto cancelado por cambios en la normativa de construcción patrimonial'
            ],
            [
                'nombre_obra' => 'Construcción Planta Tratamiento Aguas',
                'ubicacion' => 'Zona Industrial Norte, Sector Ambiental',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => Carbon::now()->addDays(45),
                'fecha_fin' => Carbon::now()->addMonths(12),
                'observaciones' => 'Planta de tratamiento con tecnología de última generación para aguas residuales'
            ],
            [
                'nombre_obra' => 'Ampliación Red Eléctrica Suburbana',
                'ubicacion' => 'Colonias Periféricas Norte y Este',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 78,
                'fecha_inicio' => Carbon::now()->subDays(150),
                'fecha_fin' => Carbon::now()->addDays(30),
                'observaciones' => 'Extensión de red eléctrica para mejorar suministro en zonas de crecimiento urbano'
            ],
            [
                'nombre_obra' => 'Construcción Terminal de Autobuses',
                'ubicacion' => 'Periférico Sur, Km 8.5',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => Carbon::now()->addDays(60),
                'fecha_fin' => Carbon::now()->addMonths(14),
                'observaciones' => 'Terminal moderna con capacidad para 50 andenes y servicios complementarios'
            ],
            [
                'nombre_obra' => 'Remodelación Hospital General Ala Norte',
                'ubicacion' => 'Hospital General, Ala Norte - Pisos 3 y 4',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 55,
                'fecha_inicio' => Carbon::now()->subDays(80),
                'fecha_fin' => Carbon::now()->addMonths(3),
                'observaciones' => 'Modernización de quirófanos y áreas de cuidados intensivos'
            ],
            [
                'nombre_obra' => 'Construcción Parque Ecológico Municipal',
                'ubicacion' => 'Terrenos Municipales, Zona Recreativa Sur',
                'estatus' => Obra::ESTATUS_SUSPENDIDA,
                'avance' => 30,
                'fecha_inicio' => Carbon::now()->subDays(70),
                'fecha_fin' => Carbon::now()->addMonths(8),
                'observaciones' => 'Proyecto suspendido por estudios adicionales de impacto ambiental'
            ],
            [
                'nombre_obra' => 'Instalación Fibra Óptica Zona Comercial',
                'ubicacion' => 'Zona Comercial Centro, 15 manzanas',
                'estatus' => Obra::ESTATUS_COMPLETADA,
                'avance' => 100,
                'fecha_inicio' => Carbon::now()->subDays(120),
                'fecha_fin' => Carbon::now()->subDays(10),
                'observaciones' => 'Red de fibra óptica instalada exitosamente con cobertura total'
            ],
            [
                'nombre_obra' => 'Construcción Escuela Primaria Colonia Nueva',
                'ubicacion' => 'Colonia Nueva Esperanza, Lote Educativo',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => Carbon::now()->addDays(90),
                'fecha_fin' => Carbon::now()->addMonths(10),
                'observaciones' => 'Escuela con capacidad para 600 alumnos y áreas deportivas'
            ]
        ];

        foreach ($obras as $obraData) {
            Obra::create($obraData);
        }

        $this->command->info('Se han creado ' . count($obras) . ' obras de prueba exitosamente.');
    }
}