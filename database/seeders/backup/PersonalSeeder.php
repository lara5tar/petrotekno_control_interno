<?php

namespace Database\Seeders;

use App\Models\Personal;
use App\Models\CategoriaPersonal;
use Illuminate\Database\Seeder;

class PersonalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener categorías existentes
        $categoriaOperador = CategoriaPersonal::where('nombre_categoria', 'Operador')->first();
        $categoriaSupervisor = CategoriaPersonal::where('nombre_categoria', 'Supervisor')->first();
        $categoriaAdministrador = CategoriaPersonal::where('nombre_categoria', 'Administrador')->first();
        $categoriaTecnico = CategoriaPersonal::where('nombre_categoria', 'Técnico')->first();
        $categoriaMecanico = CategoriaPersonal::where('nombre_categoria', 'Mecánico')->first();
        $categoriaJefeObra = CategoriaPersonal::where('nombre_categoria', 'Jefe de Obra')->first();

        // Personal específico con datos realistas
        $personal = [
            // Operadores
            [
                'nombre_completo' => 'Juan Carlos Pérez Martínez',
                'estatus' => 'activo',
                'categoria_id' => $categoriaOperador->id,
                'curp_numero' => 'PEMJ850315HDFRRN09',
                'rfc' => 'PEMJ850315ABC',
                'nss' => '12345678901',
                'no_licencia' => 'LIC001234567',
                'direccion' => 'Av. Principal #123, Col. Centro',
            ],
            [
                'nombre_completo' => 'María Elena González Rodríguez',
                'estatus' => 'activo',
                'categoria_id' => $categoriaOperador->id,
                'curp_numero' => 'GORM900522MDFNDR08',
                'rfc' => 'GORM900522XYZ',
                'nss' => '23456789012',
                'no_licencia' => 'LIC002345678',
                'direccion' => 'Calle Secundaria #456, Col. Norte',
            ],
            [
                'nombre_completo' => 'Roberto Silva Hernández',
                'estatus' => 'activo',
                'categoria_id' => $categoriaOperador->id,
                'curp_numero' => 'SIHR880710HDFLRB07',
                'rfc' => 'SIHR880710DEF',
                'nss' => '34567890123',
                'no_licencia' => 'LIC003456789',
                'direccion' => 'Blvd. Industrial #789, Col. Sur',
            ],
            [
                'nombre_completo' => 'Ana Patricia López Vázquez',
                'estatus' => 'activo',
                'categoria_id' => $categoriaOperador->id,
                'curp_numero' => 'LOVA920318MDFPZN06',
                'rfc' => 'LOVA920318GHI',
                'nss' => '45678901234',
                'no_licencia' => 'LIC004567890',
                'direccion' => 'Av. Revolución #321, Col. Este',
            ],
            [
                'nombre_completo' => 'Carlos Eduardo Ramírez Torres',
                'estatus' => 'activo',
                'categoria_id' => $categoriaOperador->id,
                'curp_numero' => 'RATC870925HDFMRR05',
                'rfc' => 'RATC870925JKL',
                'nss' => '56789012345',
                'no_licencia' => 'LIC005678901',
                'direccion' => 'Calle Morelos #654, Col. Oeste',
            ],

            // Supervisores
            [
                'nombre_completo' => 'Ing. Miguel Ángel Fernández Castro',
                'estatus' => 'activo',
                'categoria_id' => $categoriaSupervisor->id,
                'curp_numero' => 'FECM750812HDFNSG04',
                'rfc' => 'FECM750812MNO',
                'nss' => '67890123456',
                'no_licencia' => null,
                'direccion' => 'Residencial Los Pinos #987, Col. Ejecutiva',
            ],
            [
                'nombre_completo' => 'Lic. Sandra Beatriz Morales Jiménez',
                'estatus' => 'activo',
                'categoria_id' => $categoriaSupervisor->id,
                'curp_numero' => 'MOJS820604MDFRLND3',
                'rfc' => 'MOJS820604PQR',
                'nss' => '78901234567',
                'no_licencia' => null,
                'direccion' => 'Fraccionamiento Las Flores #147, Col. Residencial',
            ],

            // Personal Técnico
            [
                'nombre_completo' => 'Técnico Luis Fernando Aguilar Mendoza',
                'estatus' => 'activo',
                'categoria_id' => $categoriaTecnico->id,
                'curp_numero' => 'AUML890420HDFGNS02',
                'rfc' => 'AUML890420STU',
                'nss' => '89012345678',
                'no_licencia' => 'LIC006789012',
                'direccion' => 'Av. Tecnológico #258, Col. Industrial',
            ],
            [
                'nombre_completo' => 'Técnico Rosa María Delgado Sánchez',
                'estatus' => 'activo',
                'categoria_id' => $categoriaTecnico->id,
                'curp_numero' => 'DESR860115MDFGLS01',
                'rfc' => 'DESR860115VWX',
                'nss' => '90123456789',
                'no_licencia' => null,
                'direccion' => 'Calle Técnica #369, Col. Moderna',
            ],

            // Personal Administrativo
            [
                'nombre_completo' => 'Lic. Patricia Alejandra Ruiz Herrera',
                'estatus' => 'activo',
                'categoria_id' => $categoriaAdministrador->id,
                'curp_numero' => 'RUHP830728MDFZRT0',
                'rfc' => 'RUHP830728YZA',
                'nss' => '01234567890',
                'no_licencia' => null,
                'direccion' => 'Av. Administración #741, Col. Corporativa',
            ],
            [
                'nombre_completo' => 'C.P. Fernando Javier Ortega Medina',
                'estatus' => 'activo',
                'categoria_id' => $categoriaAdministrador->id,
                'curp_numero' => 'ORMF790503HDFGDN9',
                'rfc' => 'ORMF790503BCD',
                'nss' => '12345098765',
                'no_licencia' => null,
                'direccion' => 'Privada Contadores #852, Col. Profesional',
            ],

            // Personal en diferentes estatus
            [
                'nombre_completo' => 'Jorge Alberto Castillo Vargas',
                'estatus' => 'vacaciones',
                'categoria_id' => $categoriaOperador->id,
                'curp_numero' => 'CAVJ910816HDFSTR98',
                'rfc' => 'CAVJ910816EFG',
                'nss' => '23456109876',
                'no_licencia' => 'LIC007890123',
                'direccion' => 'Calle Vacacional #963, Col. Descanso',
            ],
            [
                'nombre_completo' => 'Alejandro Montes de Oca Rivera',
                'estatus' => 'suspendido',
                'categoria_id' => $categoriaOperador->id,
                'curp_numero' => 'MORA840207HDFNTR97',
                'rfc' => 'MORA840207HIJ',
                'nss' => '34567210987',
                'no_licencia' => 'LIC008901234',
                'direccion' => 'Av. Suspensión #074, Col. Temporal',
            ],
        ];

        foreach ($personal as $persona) {
            Personal::create($persona);
        }

        // Crear personal adicional usando factory para completar
        Personal::factory(5)->create();

        $this->command->info('✅ Personal creado exitosamente.');
        $this->command->info('👥 Total personal: ' . Personal::count());
        $this->command->info('🟢 Activos: ' . Personal::where('estatus', 'activo')->count());
        $this->command->info('🟡 En vacaciones: ' . Personal::where('estatus', 'vacaciones')->count());
        $this->command->info('🔴 Suspendidos: ' . Personal::where('estatus', 'suspendido')->count());
    }
}
