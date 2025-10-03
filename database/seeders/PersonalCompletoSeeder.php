<?php

namespace Database\Seeders;

use App\Models\Personal;
use App\Models\CategoriaPersonal;
use Illuminate\Database\Seeder;

class PersonalCompletoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener categorías existentes
        $categorias = CategoriaPersonal::all();
        
        if ($categorias->isEmpty()) {
            $this->command->error('No hay categorías de personal. Por favor, crea categorías primero.');
            return;
        }

        $personales = [
            [
                'nombre_completo' => 'Juan Carlos Pérez González',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'PEGJ850315HDFRNN09',
                'rfc' => 'PEGJ850315HT8',
                'nss' => '12345678901',
                'ine' => 'IDMEX1234567890',
                'no_licencia' => 'A12345678',
                'direccion' => 'Av. Reforma 123, Col. Centro, Ciudad de México, CP 06000',
            ],
            [
                'nombre_completo' => 'María Fernanda López Martínez',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'LOMF900420MDFPRL05',
                'rfc' => 'LOMF900420MR6',
                'nss' => '98765432109',
                'ine' => 'IDMEX9876543210',
                'no_licencia' => 'B98765432',
                'direccion' => 'Calle Juárez 456, Col. Jardines, Guadalajara, Jalisco, CP 44100',
            ],
            [
                'nombre_completo' => 'Roberto Carlos Sánchez Hernández',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'SAHR880712HDFNRB08',
                'rfc' => 'SAHR880712HD3',
                'nss' => '55566677788',
                'ine' => 'IDMEX5556667778',
                'no_licencia' => 'C55566677',
                'direccion' => 'Av. Universidad 789, Col. Del Valle, Monterrey, Nuevo León, CP 64000',
            ],
            [
                'nombre_completo' => 'Ana Patricia Ramírez Torres',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'RATA920525MDFDNR02',
                'rfc' => 'RATA920525MR9',
                'nss' => '11122233344',
                'ine' => 'IDMEX1112223334',
                'no_licencia' => 'D11122233',
                'direccion' => 'Blvd. Miguel Alemán 321, Col. Obrera, Puebla, Puebla, CP 72000',
            ],
            [
                'nombre_completo' => 'Luis Alberto García Flores',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'GAFL870830HDFRLS04',
                'rfc' => 'GAFL870830HL5',
                'nss' => '66677788899',
                'ine' => 'IDMEX6667778889',
                'no_licencia' => 'E66677788',
                'direccion' => 'Calle Morelos 654, Col. Americana, Querétaro, Querétaro, CP 76000',
            ],
            [
                'nombre_completo' => 'Carmen Elena Rodríguez Mendoza',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'ROMC891103MDFNDR01',
                'rfc' => 'ROMC891103MR7',
                'nss' => '22233344455',
                'ine' => 'IDMEX2223334445',
                'no_licencia' => 'F22233344',
                'direccion' => 'Av. Insurgentes 987, Col. Polanco, León, Guanajuato, CP 37000',
            ],
            [
                'nombre_completo' => 'Jorge Enrique Morales Castro',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'MOCJ860215HDFNSR06',
                'rfc' => 'MOCJ860215HT2',
                'nss' => '77788899900',
                'ine' => 'IDMEX7778889990',
                'no_licencia' => 'G77788899',
                'direccion' => 'Calle Hidalgo 147, Col. Centro, Mérida, Yucatán, CP 97000',
            ],
            [
                'nombre_completo' => 'Sandra Liliana Cruz Vega',
                'estatus' => 'vacaciones',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'CUVS940618MDFRGN03',
                'rfc' => 'CUVS940618MR4',
                'nss' => '33344455566',
                'ine' => 'IDMEX3334445556',
                'no_licencia' => 'H33344455',
                'direccion' => 'Av. Constitución 258, Col. Zona Dorada, Tijuana, Baja California, CP 22000',
            ],
            [
                'nombre_completo' => 'Miguel Ángel Ortega Ruiz',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'OERM910925HDFRTG07',
                'rfc' => 'OERM910925HT1',
                'nss' => '88899900011',
                'ine' => 'IDMEX8889990001',
                'no_licencia' => 'I88899900',
                'direccion' => 'Calle Zaragoza 369, Col. Lindavista, Toluca, Estado de México, CP 50000',
            ],
            [
                'nombre_completo' => 'Verónica Alejandra Díaz Soto',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'DISV881207MDFTZR05',
                'rfc' => 'DISV881207MR8',
                'nss' => '44455566677',
                'ine' => 'IDMEX4445556667',
                'no_licencia' => 'J44455566',
                'direccion' => 'Blvd. Lázaro Cárdenas 741, Col. Industrial, Aguascalientes, Aguascalientes, CP 20000',
            ],
            [
                'nombre_completo' => 'Francisco Javier Vargas Núñez',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'VANF930410HDFRLR09',
                'rfc' => 'VANF930410HT6',
                'nss' => '99900011122',
                'ine' => 'IDMEX9990001112',
                'no_licencia' => 'K99900011',
                'direccion' => 'Av. Tecnológico 852, Col. Torreón Jardín, Torreón, Coahuila, CP 27000',
            ],
            [
                'nombre_completo' => 'Gabriela Monserrat Herrera Campos',
                'estatus' => 'inactivo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'HECG950822MDFRMB02',
                'rfc' => 'HECG950822MR3',
                'nss' => '55566677799',
                'ine' => 'IDMEX5556667779',
                'no_licencia' => 'L55566677',
                'direccion' => 'Calle Allende 963, Col. San Pedro, Chihuahua, Chihuahua, CP 31000',
            ],
            [
                'nombre_completo' => 'Ricardo Daniel Jiménez Reyes',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'JIRR870530HDFMYC04',
                'rfc' => 'JIRR870530HT7',
                'nss' => '10111213141',
                'ine' => 'IDMEX1011121314',
                'no_licencia' => 'M10111213',
                'direccion' => 'Av. Juárez 159, Col. Centro Histórico, Morelia, Michoacán, CP 58000',
            ],
            [
                'nombre_completo' => 'Patricia Isabel Castillo Paredes',
                'estatus' => 'activo',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'CAPI920714MDFSPB06',
                'rfc' => 'CAPI920714MR2',
                'nss' => '15161718192',
                'ine' => 'IDMEX1516171819',
                'no_licencia' => 'N15161718',
                'direccion' => 'Blvd. Venustiano Carranza 357, Col. Moderna, Veracruz, Veracruz, CP 91700',
            ],
            [
                'nombre_completo' => 'Héctor Manuel Contreras Silva',
                'estatus' => 'suspendido',
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => 'COSH840908HDFNLC08',
                'rfc' => 'COSH840908HT4',
                'nss' => '20212223242',
                'ine' => 'IDMEX2021222324',
                'no_licencia' => 'O20212223',
                'direccion' => 'Calle Independencia 468, Col. Chapultepec, Culiacán, Sinaloa, CP 80000',
            ],
        ];

        foreach ($personales as $personalData) {
            Personal::create($personalData);
        }

        $this->command->info('Se crearon ' . count($personales) . ' registros de personal con datos completos.');
    }
}
