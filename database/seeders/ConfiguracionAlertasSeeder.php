<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionAlertasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            // Configuraciones generales
            [
                'tipo_config' => 'general',
                'clave' => 'alerta_inmediata',
                'valor' => 'true',
                'descripcion' => 'Enviar alerta al momento de detectar vencimiento',
                'activo' => true
            ],
            [
                'tipo_config' => 'general',
                'clave' => 'recordatorios_activos',
                'valor' => 'true',
                'descripcion' => 'Enviar recordatorios diarios',
                'activo' => true
            ],
            [
                'tipo_config' => 'general',
                'clave' => 'cooldown_horas',
                'valor' => '4',
                'descripcion' => 'Horas de espera entre alertas del mismo vehículo',
                'activo' => true
            ],
            
            // Configuraciones de horarios
            [
                'tipo_config' => 'horarios',
                'clave' => 'hora_envio_diario',
                'valor' => '08:00',
                'descripcion' => 'Hora del día para envío de recordatorios',
                'activo' => true
            ],
            [
                'tipo_config' => 'horarios',
                'clave' => 'dias_semana',
                'valor' => '["lunes","martes","miercoles","jueves","viernes"]',
                'descripcion' => 'Días de la semana para enviar alertas',
                'activo' => true
            ],
            
            // Configuraciones de destinatarios
            [
                'tipo_config' => 'destinatarios',
                'clave' => 'emails_principales',
                'valor' => '["ebravotube@gmail.com"]',
                'descripcion' => 'Lista de emails principales para alertas',
                'activo' => true
            ],
            [
                'tipo_config' => 'destinatarios',
                'clave' => 'emails_copia',
                'valor' => '[]',
                'descripcion' => 'Lista de emails en copia para alertas',
                'activo' => true
            ],
        ];

        foreach ($configuraciones as $config) {
            DB::table('configuracion_alertas')->updateOrInsert(
                [
                    'tipo_config' => $config['tipo_config'],
                    'clave' => $config['clave']
                ],
                array_merge($config, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }
}
