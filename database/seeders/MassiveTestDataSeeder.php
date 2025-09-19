<?php

namespace Database\Seeders;

use App\Models\AsignacionObra;
use App\Models\CategoriaPersonal;
use App\Models\CatalogoEstatus;
use App\Models\CatalogoTipoDocumento;
use App\Models\ConfiguracionAlerta;
use App\Models\Documento;
use App\Models\Kilometraje;
use App\Models\Mantenimiento;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\Role;
use App\Models\TipoActivo;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class MassiveTestDataSeeder extends Seeder
{
    private $faker;
    
    public function __construct()
    {
        $this->faker = Faker::create('es_ES');
    }

    /**
     * Ejecutar el seeder de datos masivos de prueba
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando generación masiva de datos de prueba...');
        
        DB::transaction(function () {
            // 1. Datos base y catálogos
            $this->seedCatalogos();
            
            // 2. Usuarios adicionales
            $this->seedUsuarios();
            
            // 3. Personal masivo
            $this->seedPersonal();
            
            // 4. Vehículos masivos
            $this->seedVehiculos();
            
            // 5. Obras masivas
            $this->seedObras();
            
            // 6. Asignaciones
            $this->seedAsignaciones();
            
            // 7. Kilometrajes
            $this->seedKilometrajes();
            
            // 8. Mantenimientos
            $this->seedMantenimientos();
            
            // 9. Documentos
            $this->seedDocumentos();
        });
        
        $this->command->info('✅ Generación masiva de datos completada exitosamente!');
        $this->mostrarResumen();
    }

    /**
     * Crear catálogos base
     */
    private function seedCatalogos(): void
    {
        $this->command->info('📋 Creando catálogos base...');
        
        // Estatus de vehículos
        $estatusVehiculos = [
            ['nombre_estatus' => 'Disponible', 'descripcion' => 'Vehículo disponible para asignación'],
            ['nombre_estatus' => 'En Uso', 'descripcion' => 'Vehículo actualmente en uso'],
            ['nombre_estatus' => 'Mantenimiento', 'descripcion' => 'Vehículo en mantenimiento'],
            ['nombre_estatus' => 'Fuera de Servicio', 'descripcion' => 'Vehículo fuera de servicio'],
            ['nombre_estatus' => 'Reparación', 'descripcion' => 'Vehículo en reparación'],
        ];
        
        foreach ($estatusVehiculos as $estatus) {
            CatalogoEstatus::updateOrCreate(
                ['nombre_estatus' => $estatus['nombre_estatus']],
                $estatus
            );
        }
        
        // Tipos de documentos
        $tiposDocumentos = [
            ['nombre_tipo_documento' => 'Licencia de Conducir', 'descripcion' => 'Licencia de conducir del personal', 'requiere_vencimiento' => true],
            ['nombre_tipo_documento' => 'INE', 'descripcion' => 'Identificación oficial', 'requiere_vencimiento' => false],
            ['nombre_tipo_documento' => 'CURP', 'descripcion' => 'Clave Única de Registro de Población', 'requiere_vencimiento' => false],
            ['nombre_tipo_documento' => 'RFC', 'descripcion' => 'Registro Federal de Contribuyentes', 'requiere_vencimiento' => false],
            ['nombre_tipo_documento' => 'Comprobante Domicilio', 'descripcion' => 'Comprobante de domicilio', 'requiere_vencimiento' => false],
            ['nombre_tipo_documento' => 'Tarjeta Circulación', 'descripcion' => 'Tarjeta de circulación del vehículo', 'requiere_vencimiento' => true],
            ['nombre_tipo_documento' => 'Póliza Seguro', 'descripcion' => 'Póliza de seguro del vehículo', 'requiere_vencimiento' => true],
            ['nombre_tipo_documento' => 'Factura Vehículo', 'descripcion' => 'Factura de compra del vehículo', 'requiere_vencimiento' => false],
            ['nombre_tipo_documento' => 'Verificación Vehicular', 'descripcion' => 'Verificación vehicular', 'requiere_vencimiento' => true],
            ['nombre_tipo_documento' => 'Contrato Obra', 'descripcion' => 'Contrato de la obra', 'requiere_vencimiento' => false],
            ['nombre_tipo_documento' => 'Fianza', 'descripcion' => 'Fianza de la obra', 'requiere_vencimiento' => true],
            ['nombre_tipo_documento' => 'Acta Entrega-Recepción', 'descripcion' => 'Acta de entrega-recepción', 'requiere_vencimiento' => false],
            ['nombre_tipo_documento' => 'Orden Servicio', 'descripcion' => 'Orden de servicio de mantenimiento', 'requiere_vencimiento' => false],
            ['nombre_tipo_documento' => 'Factura Mantenimiento', 'descripcion' => 'Factura del mantenimiento', 'requiere_vencimiento' => false],
        ];
        
        foreach ($tiposDocumentos as $tipo) {
            CatalogoTipoDocumento::updateOrCreate(
                ['nombre_tipo_documento' => $tipo['nombre_tipo_documento']],
                $tipo
            );
        }
        
        // Configuración de alertas
        $configuraciones = [
            ['tipo_config' => 'general', 'clave' => 'dias_anticipacion_mantenimiento', 'valor' => '30', 'descripcion' => 'Días de anticipación para alertas de mantenimiento'],
            ['tipo_config' => 'general', 'clave' => 'dias_anticipacion_documentos', 'valor' => '15', 'descripcion' => 'Días de anticipación para vencimiento de documentos'],
            ['tipo_config' => 'horarios', 'clave' => 'hora_envio_alertas', 'valor' => '08:00', 'descripcion' => 'Hora de envío de alertas diarias'],
            ['tipo_config' => 'destinatarios', 'clave' => 'emails_alertas', 'valor' => '["admin@petrotekno.com","supervisor@petrotekno.com"]', 'descripcion' => 'Emails para recibir alertas'],
        ];
        
        foreach ($configuraciones as $config) {
            ConfiguracionAlerta::updateOrCreate(
                ['tipo_config' => $config['tipo_config'], 'clave' => $config['clave']],
                $config
            );
        }
    }

    /**
     * Crear usuarios adicionales
     */
    private function seedUsuarios(): void
    {
        $this->command->info('👥 Creando usuarios adicionales...');
        
        $roles = Role::all();
        
        // Crear 15 usuarios adicionales
        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'email' => $this->faker->unique()->safeEmail(),
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            
            // Asignar rol aleatorio
            if ($roles->isNotEmpty()) {
                $user->update(['rol_id' => $roles->random()->id]);
            }
        }
    }

    /**
     * Crear personal masivo
     */
    private function seedPersonal(): void
    {
        $this->command->info('👷 Creando personal masivo (120 registros)...');
        
        $categorias = CategoriaPersonal::all();
        
        for ($i = 1; $i <= 120; $i++) {
            Personal::create([
                'nombre_completo' => $this->faker->name(),
                'estatus' => $this->faker->randomElement(['activo', 'inactivo']),
                'categoria_id' => $categorias->random()->id,
                'curp_numero' => $this->generateCURP(),
                'rfc' => $this->generateRFC(),
                'nss' => $this->faker->numerify('###########'),
                'no_licencia' => $this->faker->optional(0.7)->bothify('??######'),
                'direccion' => $this->faker->address(),
                'ine' => $this->faker->numerify('#############'),
            ]);
        }
    }

    /**
     * Crear vehículos masivos
     */
    private function seedVehiculos(): void
    {
        $this->command->info('🚗 Creando vehículos masivos (60 registros)...');
        
        $tiposActivo = TipoActivo::all();
        $marcas = ['Toyota', 'Ford', 'Chevrolet', 'Nissan', 'Honda', 'Volkswagen', 'Hyundai', 'Kia', 'Mazda', 'Mitsubishi'];
        $modelos = [
            'Toyota' => ['Hilux', 'Corolla', 'Camry', 'RAV4', 'Prius', 'Tacoma'],
            'Ford' => ['F-150', 'Explorer', 'Focus', 'Escape', 'Ranger', 'Transit'],
            'Chevrolet' => ['Silverado', 'Equinox', 'Malibu', 'Suburban', 'Colorado', 'Express'],
            'Nissan' => ['Frontier', 'Sentra', 'Altima', 'Pathfinder', 'Titan', 'NV200'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Ridgeline', 'Odyssey'],
            'Volkswagen' => ['Jetta', 'Passat', 'Tiguan', 'Atlas', 'Amarok', 'Crafter'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'H100', 'Porter'],
            'Kia' => ['Forte', 'Optima', 'Sorento', 'Sportage', 'Bongo', 'K2500'],
            'Mazda' => ['Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'BT-50', 'Bongo'],
            'Mitsubishi' => ['Lancer', 'Outlander', 'Montero', 'L200', 'Canter', 'Fuso'],
        ];
        
        $estados = ['disponible', 'asignado', 'en_mantenimiento', 'fuera_de_servicio', 'baja'];
        
        for ($i = 1; $i <= 60; $i++) {
            $marca = $this->faker->randomElement($marcas);
            $modelo = $this->faker->randomElement($modelos[$marca]);
            
            Vehiculo::create([
                'marca' => $marca,
                'modelo' => $modelo,
                'anio' => $this->faker->numberBetween(2010, 2024),
                'n_serie' => strtoupper($this->faker->unique()->bothify('???######')),
                'placas' => strtoupper($this->faker->unique()->bothify('???-###')),
                'estatus' => $this->faker->randomElement(['disponible', 'asignado', 'en_mantenimiento', 'fuera_de_servicio', 'baja']),
                'kilometraje_actual' => $this->faker->numberBetween(0, 300000),
                'intervalo_km_motor' => $this->faker->randomElement([5000, 7500, 10000]),
                'intervalo_km_transmision' => $this->faker->randomElement([40000, 60000, 80000]),
                'intervalo_km_hidraulico' => $this->faker->randomElement([20000, 30000, 40000]),
                'observaciones' => $this->faker->optional(0.3)->sentence(),
                'tipo_activo_id' => $tiposActivo->random()->id,
                'estado' => $this->faker->randomElement(['Aguascalientes', 'Baja California', 'Sonora', 'Chihuahua', 'Nuevo León']),
                'municipio' => $this->faker->city(),
            ]);
        }
    }

    /**
     * Crear obras masivas
     */
    private function seedObras(): void
    {
        $this->command->info('🏗️ Creando obras masivas (40 registros)...');
        
        $personal = Personal::where('estatus', 'activo')->get();
        $vehiculos = Vehiculo::where('estatus', 'disponible')->get();
        $usuarios = User::all();
        
        $tiposObra = [
            'Construcción de Carretera',
            'Pavimentación Urbana',
            'Construcción de Puente',
            'Mantenimiento Vial',
            'Obra Hidráulica',
            'Construcción Residencial',
            'Obra Industrial',
            'Infraestructura Eléctrica',
            'Construcción Comercial',
            'Rehabilitación Urbana'
        ];
        
        for ($i = 1; $i <= 40; $i++) {
            $fechaInicio = $this->faker->dateTimeBetween('-1 year', '+6 months');
            $fechaFin = $this->faker->optional(0.7)->dateTimeBetween($fechaInicio, '+1 year');
            
            $obra = Obra::create([
                'nombre_obra' => $this->faker->randomElement($tiposObra) . ' ' . $this->faker->city() . ' ' . $i,
                'estatus' => $this->faker->randomElement(['planificada', 'en_progreso', 'suspendida', 'completada']),
                'avance' => $this->faker->numberBetween(0, 100),
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin ? $fechaFin->format('Y-m-d') : null,
                'ubicacion' => $this->faker->address(),
                'observaciones' => $this->faker->optional(0.4)->paragraph(),
            ]);
            
            // Asignar encargado (personal) si está disponible
            if ($personal->count() > 0) {
                $obra->update(['encargado_id' => $personal->random()->id]);
            }
        }
    }

    /**
     * Crear asignaciones
     */
    private function seedAsignaciones(): void
    {
        $this->command->info('📋 Creando asignaciones...');
        
        $obras = Obra::all();
        $vehiculos = Vehiculo::all();
        $personal = Personal::all();
        $usuarios = User::all();
        
        // Crear asignaciones evitando duplicados por la restricción unique_vehiculo_activo
        $vehiculosUsados = collect();
        $numAsignaciones = min(30, $vehiculos->count()); // No más asignaciones que vehículos disponibles
        
        for ($i = 0; $i < $numAsignaciones; $i++) {
            if ($obras->isNotEmpty() && $vehiculos->isNotEmpty() && $personal->isNotEmpty() && $usuarios->isNotEmpty()) {
                // Seleccionar un vehículo que no tenga asignación activa
                $vehiculosDisponibles = $vehiculos->reject(function($vehiculo) use ($vehiculosUsados) {
                    return $vehiculosUsados->contains($vehiculo->id);
                });
                
                if ($vehiculosDisponibles->isEmpty()) {
                    break; // No hay más vehículos disponibles
                }
                
                $obra = $obras->random();
                $vehiculo = $vehiculosDisponibles->random();
                $operador = $personal->random();
                $encargado = $usuarios->random();
                
                $fechaAsignacion = $this->faker->dateTimeBetween('-1 year', 'now');
                // 70% de probabilidad de que esté liberada (para evitar muchas asignaciones activas)
                $fechaLiberacion = $this->faker->boolean(70) ? 
                    $this->faker->dateTimeBetween($fechaAsignacion, 'now') : null;
                
                try {
                    AsignacionObra::create([
                        'vehiculo_id' => $vehiculo->id,
                        'obra_id' => $obra->id,
                        'operador_id' => $operador->id,
                        'fecha_asignacion' => $fechaAsignacion,
                        'fecha_liberacion' => $fechaLiberacion,
                        'kilometraje_inicial' => $this->faker->numberBetween(10000, 200000),
                        'kilometraje_final' => $fechaLiberacion ? $this->faker->numberBetween(10000, 250000) : null,
                        'observaciones' => $this->faker->optional(0.3)->sentence(),
                    ]);
                    
                    // Si la asignación está activa, marcar el vehículo como usado
                    if (!$fechaLiberacion) {
                        $vehiculosUsados->push($vehiculo->id);
                    }
                } catch (\Exception $e) {
                    // Si hay error por duplicado, continuar con el siguiente
                    continue;
                }
            }
        }
    }

    /**
     * Crear kilometrajes
     */
    private function seedKilometrajes(): void
    {
        $this->command->info('📊 Creando registros de kilometrajes...');
        
        $vehiculos = Vehiculo::all();
        $usuarios = User::all();
        $obras = Obra::all();
        
        foreach ($vehiculos as $vehiculo) {
            $kilometrajeBase = $vehiculo->kilometraje_actual - $this->faker->numberBetween(10000, 50000);
            
            // Crear 10-30 registros de kilometraje por vehículo
            $numRegistros = $this->faker->numberBetween(10, 30);
            
            for ($i = 0; $i < $numRegistros; $i++) {
                $kilometrajeBase += $this->faker->numberBetween(100, 2000);
                
                try {
                    Kilometraje::create([
                        'vehiculo_id' => $vehiculo->id,
                        'kilometraje' => $kilometrajeBase,
                        'fecha_captura' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                        'usuario_captura_id' => $usuarios->random()->id,
                        'obra_id' => $this->faker->optional(0.7)->randomElement($obras)->id ?? null,
                        'observaciones' => $this->faker->optional(0.2)->sentence(),
                    ]);
                } catch (\Exception $e) {
                    // Si hay error por duplicado de kilometraje, continuar
                    continue;
                }
            }
            
            // Actualizar kilometraje actual del vehículo
            $vehiculo->update(['kilometraje_actual' => $kilometrajeBase]);
        }
    }

    /**
     * Crear mantenimientos
     */
    private function seedMantenimientos(): void
    {
        $this->command->info('🔧 Creando mantenimientos...');
        
        $vehiculos = Vehiculo::all();
        $proveedores = [
            'Taller Mecánico López',
            'Servicios Automotrices García',
            'Mantenimiento Industrial Pérez',
            'Taller Especializado Rodríguez',
            'Servicios Técnicos Martínez',
            'Autoservicio Hernández',
        ];
        
        foreach ($vehiculos as $vehiculo) {
            // Crear 2-8 mantenimientos por vehículo
            $numMantenimientos = $this->faker->numberBetween(2, 8);
            
            for ($i = 0; $i < $numMantenimientos; $i++) {
                $fechaInicio = $this->faker->dateTimeBetween('-2 years', 'now');
                $fechaFin = $this->faker->optional(0.9)->dateTimeBetween($fechaInicio, '+1 week');
                
                Mantenimiento::create([
                    'vehiculo_id' => $vehiculo->id,
                    'tipo_servicio' => $this->faker->randomElement(['CORRECTIVO', 'PREVENTIVO']),
                    'sistema_vehiculo' => $this->faker->randomElement(['motor', 'transmision', 'hidraulico', 'general']),
                    'proveedor' => $this->faker->randomElement($proveedores),
                    'descripcion' => $this->faker->paragraph(),
                    'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                    'fecha_fin' => $fechaFin ? $fechaFin->format('Y-m-d') : null,
                    'kilometraje_servicio' => $this->faker->numberBetween(1000, $vehiculo->kilometraje_actual),
                    'costo' => $this->faker->randomFloat(2, 500, 15000),
                ]);
            }
        }
    }

    /**
     * Crear documentos
     */
    private function seedDocumentos(): void
    {
        $this->command->info('📄 Creando documentos...');
        
        $tiposDocumento = CatalogoTipoDocumento::all();
        $vehiculos = Vehiculo::all();
        $personal = Personal::all();
        $obras = Obra::all();
        $mantenimientos = Mantenimiento::all();
        
        // Documentos para vehículos
        foreach ($vehiculos as $vehiculo) {
            $numDocs = $this->faker->numberBetween(2, 5);
            for ($i = 0; $i < $numDocs; $i++) {
                $tipoDoc = $tiposDocumento->random();
                
                Documento::create([
                    'tipo_documento_id' => $tipoDoc->id,
                    'descripcion' => $this->faker->sentence(),
                    'ruta_archivo' => 'documentos/vehiculos/' . $this->faker->uuid() . '.pdf',
                    'fecha_vencimiento' => $tipoDoc->requiere_vencimiento ? 
                        $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d') : null,
                    'vehiculo_id' => $vehiculo->id,
                ]);
            }
        }
        
        // Documentos para personal
        foreach ($personal as $persona) {
            $numDocs = $this->faker->numberBetween(1, 4);
            for ($i = 0; $i < $numDocs; $i++) {
                $tipoDoc = $tiposDocumento->random();
                
                Documento::create([
                    'tipo_documento_id' => $tipoDoc->id,
                    'descripcion' => $this->faker->sentence(),
                    'ruta_archivo' => 'documentos/personal/' . $this->faker->uuid() . '.pdf',
                    'fecha_vencimiento' => $tipoDoc->requiere_vencimiento ? 
                        $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d') : null,
                    'personal_id' => $persona->id,
                ]);
            }
        }
        
        // Documentos para obras
        foreach ($obras as $obra) {
            $numDocs = $this->faker->numberBetween(1, 3);
            for ($i = 0; $i < $numDocs; $i++) {
                $tipoDoc = $tiposDocumento->random();
                
                Documento::create([
                    'tipo_documento_id' => $tipoDoc->id,
                    'descripcion' => $this->faker->sentence(),
                    'ruta_archivo' => 'documentos/obras/' . $this->faker->uuid() . '.pdf',
                    'fecha_vencimiento' => $tipoDoc->requiere_vencimiento ? 
                        $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d') : null,
                    'obra_id' => $obra->id,
                ]);
            }
        }
        
        // Documentos para mantenimientos
        foreach ($mantenimientos->take(50) as $mantenimiento) {
            $tipoDoc = $tiposDocumento->random();
            
            Documento::create([
                'tipo_documento_id' => $tipoDoc->id,
                'descripcion' => 'Documento de mantenimiento: ' . $this->faker->sentence(),
                'ruta_archivo' => 'documentos/mantenimientos/' . $this->faker->uuid() . '.pdf',
                'fecha_vencimiento' => $tipoDoc->requiere_vencimiento ? 
                    $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d') : null,
                'mantenimiento_id' => $mantenimiento->id,
            ]);
        }
    }

    /**
     * Generar CURP aleatorio
     */
    private function generateCURP(): string
    {
        $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeros = '0123456789';
        
        return substr(str_shuffle($letras), 0, 4) . 
               substr(str_shuffle($numeros), 0, 6) . 
               substr(str_shuffle($letras), 0, 6) . 
               substr(str_shuffle($numeros), 0, 2);
    }

    /**
     * Generar RFC aleatorio
     */
    private function generateRFC(): string
    {
        $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeros = '0123456789';
        
        return substr(str_shuffle($letras), 0, 4) . 
               substr(str_shuffle($numeros), 0, 6) . 
               substr(str_shuffle($letras . $numeros), 0, 3);
    }

    /**
     * Mostrar resumen de datos creados
     */
    private function mostrarResumen(): void
    {
        $this->command->info('\n📊 RESUMEN DE DATOS GENERADOS:');
        $this->command->info('👥 Usuarios: ' . User::count());
        $this->command->info('👷 Personal: ' . Personal::count());
        $this->command->info('🚗 Vehículos: ' . Vehiculo::count());
        $this->command->info('🏗️ Obras: ' . Obra::count());
        $this->command->info('📋 Asignaciones: ' . AsignacionObra::count());
        $this->command->info('📊 Kilometrajes: ' . Kilometraje::count());
        $this->command->info('🔧 Mantenimientos: ' . Mantenimiento::count());
        $this->command->info('📄 Documentos: ' . Documento::count());
        $this->command->info('\n🎯 ¡Datos de prueba listos para usar!');
    }
}