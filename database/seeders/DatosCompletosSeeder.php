<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use App\Models\Personal;
use App\Models\Obra;
use App\Models\Mantenimiento;
use App\Models\TipoActivo;
use App\Models\CategoriaPersonal;
use App\Models\Kilometraje;
use App\Models\AsignacionObra;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatosCompletosSeeder extends Seeder
{
    /**
     * Seeder completo para crear registros de prueba en los modelos principales
     * Vehículos, Personal, Obras y Mantenimientos
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando creación de datos completos del sistema...');
        $this->command->newLine();

        // 1. Crear Personal
        $personal = $this->crearPersonal();
        
        // 2. Crear Vehículos
        $vehiculos = $this->crearVehiculos();
        
        // 3. Crear Obras
        $obras = $this->crearObras();
        
        // 4. Crear Mantenimientos
        $this->crearMantenimientos($vehiculos);
        
        // 5. Crear Kilometrajes
        $this->crearKilometrajes($vehiculos);
        
        // 6. Asignar vehículos a obras
        $this->asignarVehiculosObras($vehiculos, $obras, $personal);

        $this->command->newLine();
        $this->command->info('✅ Datos completos creados exitosamente!');
    }

    /**
     * Crear registros de Personal
     */
    private function crearPersonal(): array
    {
        $this->command->info('👥 Creando personal...');
        
        $categorias = CategoriaPersonal::all();
        if ($categorias->isEmpty()) {
            $this->command->warn('⚠️  No hay categorías de personal. Ejecuta primero los seeders básicos.');
            return [];
        }

        $catOperador = $categorias->where('nombre', 'Operador')->first();
        $catResponsable = $categorias->where('nombre', 'Responsable de obra')->first();
        $catAdmin = $categorias->where('nombre', 'Administrador del sistema')->first();

        $personalData = [
            [
                'nombre_completo' => 'JUAN PÉREZ GARCÍA',
                'estatus' => 'activo',
                'categoria_id' => $catOperador?->id ?? 1,
                'curp_numero' => 'PEGJ850615HNLRZN05',
                'rfc' => 'PEGJ850615XY9',
                'nss' => '12345678901',
                'no_licencia' => 'MTY123456',
                'direccion' => 'AV. CONSTITUCIÓN #123, COL. CENTRO',
                'ine' => 'IDMEX123456789',
            ],
            [
                'nombre_completo' => 'MARÍA GONZÁLEZ LÓPEZ',
                'estatus' => 'activo',
                'categoria_id' => $catOperador?->id ?? 1,
                'curp_numero' => 'GOLM900325MNLPPR08',
                'rfc' => 'GOLM900325AB3',
                'nss' => '98765432109',
                'no_licencia' => 'MTY987654',
                'direccion' => 'CALLE MORELOS #456, COL. OBRERA',
                'ine' => 'IDMEX987654321',
            ],
            [
                'nombre_completo' => 'CARLOS MARTÍNEZ HERNÁNDEZ',
                'estatus' => 'activo',
                'categoria_id' => $catResponsable?->id ?? 2,
                'curp_numero' => 'MAHC870820HNLRRR02',
                'rfc' => 'MAHC870820CD5',
                'nss' => '45678912345',
                'direccion' => 'BLVD. FUNDADORES #789, COL. RESIDENCIAL',
            ],
            [
                'nombre_completo' => 'ANA RODRÍGUEZ SÁNCHEZ',
                'estatus' => 'activo',
                'categoria_id' => $catResponsable?->id ?? 2,
                'curp_numero' => 'ROSA920515MNLDDN09',
                'rfc' => 'ROSA920515EF7',
                'nss' => '78912345678',
                'direccion' => 'AV. UNIVERSIDAD #321, COL. TECNOLÓGICO',
            ],
            [
                'nombre_completo' => 'ROBERTO LÓPEZ TORRES',
                'estatus' => 'activo',
                'categoria_id' => $catOperador?->id ?? 1,
                'curp_numero' => 'LOTR880710HNLPRB06',
                'rfc' => 'LOTR880710GH8',
                'nss' => '32165498701',
                'no_licencia' => 'GDL456789',
                'direccion' => 'CALLE JUÁREZ #654, COL. REFORMA',
            ],
            [
                'nombre_completo' => 'LAURA FERNÁNDEZ RUIZ',
                'estatus' => 'inactivo',
                'categoria_id' => $catOperador?->id ?? 1,
                'curp_numero' => 'FERL950205MNLRNR03',
                'rfc' => 'FERL950205IJ9',
                'nss' => '65432198765',
                'direccion' => 'AV. REVOLUCIÓN #987, COL. INDEPENDENCIA',
            ],
        ];

        $personalCreados = [];
        foreach ($personalData as $data) {
            try {
                $personalCreados[] = Personal::create($data);
            } catch (\Exception $e) {
                $this->command->warn("Error al crear personal: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$this->contarRegistros($personalCreados)}/{$this->contarRegistros($personalData)} personal creados");
        return $personalCreados;
    }

    /**
     * Crear registros de Vehículos
     */
    private function crearVehiculos(): array
    {
        $this->command->info('🚗 Creando vehículos...');
        
        $tiposActivo = TipoActivo::all();
        if ($tiposActivo->isEmpty()) {
            $this->command->warn('⚠️  No hay tipos de activo. Ejecuta primero los seeders básicos.');
            return [];
        }

        $tipoTransporte = $tiposActivo->where('tiene_kilometraje', true)->first();
        $tipoMaquinaria = $tiposActivo->where('tiene_kilometraje', false)->first();

        $vehiculosData = [
            // Vehículos de transporte
            [
                'marca' => 'FORD',
                'modelo' => 'F-150 XLT',
                'anio' => 2022,
                'n_serie' => '1FTEW1EP5NKE12345',
                'placas' => 'ABC-123-D',
                'tipo_activo_id' => $tipoTransporte?->id ?? 1,
                'estatus' => 'disponible',
                'kilometraje_actual' => 15230,
                'estado' => 'NUEVO LEÓN',
                'municipio' => 'MONTERREY',
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 80000,
                'observaciones' => 'CAMIONETA EN EXCELENTE ESTADO, MANTENIMIENTO AL DÍA',
                'numero_poliza' => 'POL-2022-001',
            ],
            [
                'marca' => 'NISSAN',
                'modelo' => 'NP300 FRONTIER',
                'anio' => 2023,
                'n_serie' => '3N6AD33A8NK901234',
                'placas' => 'GHI-789-F',
                'tipo_activo_id' => $tipoTransporte?->id ?? 1,
                'estatus' => 'asignado',
                'kilometraje_actual' => 8450,
                'estado' => 'NUEVO LEÓN',
                'municipio' => 'SAN PEDRO GARZA GARCÍA',
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 80000,
                'observaciones' => 'UNIDAD NUEVA, PRIMER SERVICIO PENDIENTE',
                'numero_poliza' => 'POL-2023-015',
            ],
            [
                'marca' => 'CHEVROLET',
                'modelo' => 'SILVERADO 1500',
                'anio' => 2020,
                'n_serie' => '1GC4K0E85LF123456',
                'placas' => 'JKL-012-G',
                'tipo_activo_id' => $tipoTransporte?->id ?? 1,
                'estatus' => 'en_mantenimiento',
                'kilometraje_actual' => 67340,
                'estado' => 'NUEVO LEÓN',
                'municipio' => 'APODACA',
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 80000,
                'observaciones' => 'EN SERVICIO DE TRANSMISIÓN, FECHA ESTIMADA: 20/10/2025',
                'numero_poliza' => 'POL-2020-078',
            ],
            [
                'marca' => 'TOYOTA',
                'modelo' => 'HILUX DOBLE CABINA',
                'anio' => 2019,
                'n_serie' => 'AHTFR22G500123456',
                'placas' => 'VWX-234-K',
                'tipo_activo_id' => $tipoTransporte?->id ?? 1,
                'estatus' => 'disponible',
                'kilometraje_actual' => 145680,
                'estado' => 'JALISCO',
                'municipio' => 'GUADALAJARA',
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 100000,
                'observaciones' => 'KILOMETRAJE ALTO, UNIDAD OPERATIVA',
                'numero_poliza' => 'POL-2019-045',
            ],
            [
                'marca' => 'RAM',
                'modelo' => '2500 HEAVY DUTY',
                'anio' => 2021,
                'n_serie' => '3C6UR5DL6MG123789',
                'placas' => 'MNO-567-H',
                'tipo_activo_id' => $tipoTransporte?->id ?? 1,
                'estatus' => 'disponible',
                'kilometraje_actual' => 32100,
                'estado' => 'NUEVO LEÓN',
                'municipio' => 'GUADALUPE',
                'intervalo_km_motor' => 8000,
                'intervalo_km_transmision' => 120000,
                'numero_poliza' => 'POL-2021-092',
            ],
            [
                'marca' => 'ISUZU',
                'modelo' => 'NPR 816',
                'anio' => 2018,
                'n_serie' => 'JALC4W16C8J012345',
                'placas' => 'PQR-890-I',
                'tipo_activo_id' => $tipoTransporte?->id ?? 1,
                'estatus' => 'fuera_de_servicio',
                'kilometraje_actual' => 198450,
                'estado' => 'NUEVO LEÓN',
                'municipio' => 'SANTA CATARINA',
                'intervalo_km_motor' => 10000,
                'observaciones' => 'REQUIERE REPARACIÓN MAYOR DE MOTOR',
                'numero_poliza' => 'POL-2018-031',
            ],
            
            // Maquinaria sin kilometraje
            [
                'marca' => 'CATERPILLAR',
                'modelo' => 'GENERADOR XQ230',
                'anio' => 2022,
                'n_serie' => 'CAT230XQ789456',
                'placas' => 'SIN-PLACAS',
                'tipo_activo_id' => $tipoMaquinaria?->id ?? 2,
                'estatus' => 'disponible',
                'kilometraje_actual' => null,
                'estado' => 'NUEVO LEÓN',
                'municipio' => 'MONTERREY',
                'observaciones' => 'GENERADOR 230KVA, 1200 HORAS DE USO',
            ],
            [
                'marca' => 'BOMAG',
                'modelo' => 'COMPACTADOR BW211D-5',
                'anio' => 2020,
                'n_serie' => 'BOMAG211D567890',
                'placas' => 'SIN-PLACAS',
                'tipo_activo_id' => $tipoMaquinaria?->id ?? 2,
                'estatus' => 'asignado',
                'kilometraje_actual' => null,
                'estado' => 'NUEVO LEÓN',
                'municipio' => 'APODACA',
                'observaciones' => 'COMPACTADOR VIBRATORIO, OPERATIVO',
            ],
            [
                'marca' => 'JOHN DEERE',
                'modelo' => 'RETROEXCAVADORA 310L',
                'anio' => 2021,
                'n_serie' => 'JD310L456789XYZ',
                'placas' => 'SIN-PLACAS',
                'tipo_activo_id' => $tipoMaquinaria?->id ?? 2,
                'estatus' => 'disponible',
                'kilometraje_actual' => null,
                'estado' => 'JALISCO',
                'municipio' => 'ZAPOPAN',
                'observaciones' => 'MAQUINARIA PESADA, BUEN ESTADO',
            ],
            [
                'marca' => 'CASE',
                'modelo' => 'MINICARGADOR SR210',
                'anio' => 2023,
                'n_serie' => 'CASE210SR987654',
                'placas' => 'SIN-PLACAS',
                'tipo_activo_id' => $tipoMaquinaria?->id ?? 2,
                'estatus' => 'disponible',
                'kilometraje_actual' => null,
                'estado' => 'NUEVO LEÓN',
                'municipio' => 'SAN NICOLÁS DE LOS GARZA',
                'observaciones' => 'EQUIPO NUEVO, 100 HORAS DE USO',
            ],
        ];

        $vehiculosCreados = [];
        foreach ($vehiculosData as $data) {
            try {
                $vehiculosCreados[] = Vehiculo::create($data);
            } catch (\Exception $e) {
                $this->command->warn("Error al crear vehículo: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$this->contarRegistros($vehiculosCreados)}/{$this->contarRegistros($vehiculosData)} vehículos creados");
        return $vehiculosCreados;
    }

    /**
     * Crear registros de Obras
     */
    private function crearObras(): array
    {
        $this->command->info('🏗️  Creando obras...');

        $obrasData = [
            [
                'nombre_obra' => 'CONSTRUCCIÓN PUENTE PERIFÉRICO NORTE',
                'ubicacion' => 'MONTERREY, NUEVO LEÓN',
                'estatus' => 'en_progreso',
                'avance' => 45,
                'fecha_inicio' => Carbon::parse('2024-01-15'),
                'fecha_fin' => Carbon::parse('2025-12-31'),
                'observaciones' => 'OBRA DE INFRAESTRUCTURA VIAL PRIORITARIA',
            ],
            [
                'nombre_obra' => 'AMPLIACIÓN CARRETERA ESTATAL 100',
                'ubicacion' => 'APODACA, NUEVO LEÓN',
                'estatus' => 'en_progreso',
                'avance' => 65,
                'fecha_inicio' => Carbon::parse('2023-06-01'),
                'fecha_fin' => Carbon::parse('2025-05-30'),
                'observaciones' => 'AMPLIACIÓN A 4 CARRILES',
            ],
            [
                'nombre_obra' => 'PAVIMENTACIÓN ZONA INDUSTRIAL ESCOBEDO',
                'ubicacion' => 'GENERAL ESCOBEDO, NUEVO LEÓN',
                'estatus' => 'en_progreso',
                'avance' => 30,
                'fecha_inicio' => Carbon::parse('2024-08-01'),
                'fecha_fin' => Carbon::parse('2025-03-31'),
            ],
            [
                'nombre_obra' => 'REHABILITACIÓN VIALIDADES CENTRO HISTÓRICO',
                'ubicacion' => 'GUADALAJARA, JALISCO',
                'estatus' => 'planificada',
                'avance' => 0,
                'fecha_inicio' => Carbon::parse('2025-02-01'),
                'fecha_fin' => Carbon::parse('2025-11-30'),
                'observaciones' => 'PROYECTO EN FASE DE PLANEACIÓN',
            ],
            [
                'nombre_obra' => 'CONSTRUCCIÓN BOULEVARD AEROPUERTO',
                'ubicacion' => 'SAN PEDRO GARZA GARCÍA, NUEVO LEÓN',
                'estatus' => 'completada',
                'avance' => 100,
                'fecha_inicio' => Carbon::parse('2022-03-15'),
                'fecha_fin' => Carbon::parse('2024-09-30'),
                'observaciones' => 'OBRA ENTREGADA Y EN OPERACIÓN',
            ],
            [
                'nombre_obra' => 'MODERNIZACIÓN LIBRAMIENTO SUR',
                'ubicacion' => 'SANTA CATARINA, NUEVO LEÓN',
                'estatus' => 'suspendida',
                'avance' => 25,
                'fecha_inicio' => Carbon::parse('2024-04-01'),
                'fecha_fin' => null,
                'observaciones' => 'SUSPENDIDA POR REVISIÓN DE PROYECTO',
            ],
            [
                'nombre_obra' => 'REPAVIMENTACIÓN AVENIDA CONSTITUCIÓN',
                'ubicacion' => 'MONTERREY, NUEVO LEÓN',
                'estatus' => 'en_progreso',
                'avance' => 80,
                'fecha_inicio' => Carbon::parse('2024-09-01'),
                'fecha_fin' => Carbon::parse('2025-01-15'),
            ],
        ];

        $obrasCreadas = [];
        foreach ($obrasData as $data) {
            try {
                $obrasCreadas[] = Obra::create($data);
            } catch (\Exception $e) {
                $this->command->warn("Error al crear obra: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$this->contarRegistros($obrasCreadas)}/{$this->contarRegistros($obrasData)} obras creadas");
        return $obrasCreadas;
    }

    /**
     * Crear registros de Mantenimientos
     */
    private function crearMantenimientos(array $vehiculos): void
    {
        if (empty($vehiculos)) {
            $this->command->warn('⚠️  No hay vehículos para crear mantenimientos');
            return;
        }

        $this->command->info('🔧 Creando mantenimientos...');

        $mantenimientos = [];

        // Mantenimientos para algunos vehículos
        foreach (array_slice($vehiculos, 0, 6) as $index => $vehiculo) {
            // Mantenimiento preventivo antiguo
            $mantenimientos[] = [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'PREVENTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => 'SERVICIO AUTOMOTRIZ DEL NORTE',
                'descripcion' => 'CAMBIO DE ACEITE Y FILTRO DE MOTOR',
                'fecha_inicio' => Carbon::now()->subMonths(6),
                'fecha_fin' => Carbon::now()->subMonths(6)->addDays(1),
                'kilometraje_servicio' => $vehiculo->kilometraje_actual ? $vehiculo->kilometraje_actual - 5000 : null,
                'costo' => rand(800, 1500),
            ];

            // Mantenimiento más reciente
            if ($index < 4) {
                $mantenimientos[] = [
                    'vehiculo_id' => $vehiculo->id,
                    'tipo_servicio' => 'PREVENTIVO',
                    'sistema_vehiculo' => 'general',
                    'proveedor' => 'TALLER MECÁNICO GARCÍA',
                    'descripcion' => 'REVISIÓN GENERAL DE 6 MESES, ROTACIÓN DE NEUMÁTICOS',
                    'fecha_inicio' => Carbon::now()->subMonths(2),
                    'fecha_fin' => Carbon::now()->subMonths(2)->addDays(2),
                    'kilometraje_servicio' => $vehiculo->kilometraje_actual ? $vehiculo->kilometraje_actual - 2000 : null,
                    'costo' => rand(1500, 3000),
                ];
            }

            // Mantenimiento correctivo para algunos
            if ($index < 2) {
                $mantenimientos[] = [
                    'vehiculo_id' => $vehiculo->id,
                    'tipo_servicio' => 'CORRECTIVO',
                    'sistema_vehiculo' => 'transmision',
                    'proveedor' => 'TRANSMISIONES ESPECIALIZADAS SA',
                    'descripcion' => 'REPARACIÓN DE SINCRONIZADORES, CAMBIO DE ACEITE ATF',
                    'fecha_inicio' => Carbon::now()->subMonths(1),
                    'fecha_fin' => Carbon::now()->subMonths(1)->addDays(5),
                    'kilometraje_servicio' => $vehiculo->kilometraje_actual ? $vehiculo->kilometraje_actual - 1000 : null,
                    'costo' => rand(8000, 15000),
                ];
            }
        }

        $created = 0;
        foreach ($mantenimientos as $data) {
            try {
                Mantenimiento::create($data);
                $created++;
            } catch (\Exception $e) {
                $this->command->warn("Error al crear mantenimiento: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$created}/{$this->contarRegistros($mantenimientos)} mantenimientos creados");
    }

    /**
     * Crear registros de Kilometrajes
     */
    private function crearKilometrajes(array $vehiculos): void
    {
        if (empty($vehiculos)) {
            return;
        }

        $this->command->info('📊 Creando registros de kilometraje...');

        $kilometrajes = [];
        
        // Solo para vehículos con kilometraje
        $vehiculosConKm = array_filter($vehiculos, fn($v) => $v->kilometraje_actual !== null);

        foreach ($vehiculosConKm as $vehiculo) {
            // Registro inicial (hace 6 meses)
            $kilometrajes[] = [
                'vehiculo_id' => $vehiculo->id,
                'kilometraje' => max(0, $vehiculo->kilometraje_actual - 6000),
                'fecha_captura' => Carbon::now()->subMonths(6),
                'observaciones' => 'REGISTRO INICIAL DEL SISTEMA',
            ];

            // Registro intermedio (hace 3 meses)
            $kilometrajes[] = [
                'vehiculo_id' => $vehiculo->id,
                'kilometraje' => max(0, $vehiculo->kilometraje_actual - 3000),
                'fecha_captura' => Carbon::now()->subMonths(3),
                'observaciones' => 'ACTUALIZACIÓN TRIMESTRAL',
            ];

            // Registro reciente (hace 1 mes)
            $kilometrajes[] = [
                'vehiculo_id' => $vehiculo->id,
                'kilometraje' => max(0, $vehiculo->kilometraje_actual - 500),
                'fecha_captura' => Carbon::now()->subMonth(),
                'observaciones' => 'REVISIÓN MENSUAL',
            ];

            // Registro actual
            $kilometrajes[] = [
                'vehiculo_id' => $vehiculo->id,
                'kilometraje' => $vehiculo->kilometraje_actual,
                'fecha_captura' => Carbon::now(),
                'observaciones' => 'REGISTRO ACTUAL',
            ];
        }

        $created = 0;
        foreach ($kilometrajes as $data) {
            try {
                Kilometraje::create($data);
                $created++;
            } catch (\Exception $e) {
                $this->command->warn("Error al crear kilometraje: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$created}/{$this->contarRegistros($kilometrajes)} registros de kilometraje creados");
    }

    /**
     * Asignar vehículos a obras
     */
    private function asignarVehiculosObras(array $vehiculos, array $obras, array $personal): void
    {
        if (empty($vehiculos) || empty($obras)) {
            return;
        }

        $this->command->info('🔗 Asignando vehículos a obras...');

        $asignaciones = [];

        // Asignar algunos vehículos a obras activas
        $obrasActivas = array_filter($obras, fn($o) => $o->estatus === 'en_progreso');
        
        $vehiculosParaAsignar = array_slice($vehiculos, 0, min(count($vehiculos), 5));
        
        foreach ($vehiculosParaAsignar as $index => $vehiculo) {
            if (!empty($obrasActivas) && $index < count($obrasActivas)) {
                $obra = array_values($obrasActivas)[$index];
                
                // Actualizar estatus del vehículo si no está asignado
                if ($vehiculo->estatus === 'disponible') {
                    $vehiculo->update(['estatus' => 'asignado']);
                }
                
                $asignaciones[] = [
                    'vehiculo_id' => $vehiculo->id,
                    'obra_id' => $obra->id,
                    'fecha_asignacion' => Carbon::now()->subDays(rand(10, 60)),
                    'fecha_devolucion' => null,
                    'observaciones' => 'ASIGNACIÓN ACTIVA A OBRA EN PROGRESO',
                ];
            }
        }

        $created = 0;
        foreach ($asignaciones as $data) {
            try {
                AsignacionObra::create($data);
                $created++;
            } catch (\Exception $e) {
                $this->command->warn("Error al crear asignación: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$created}/{$this->contarRegistros($asignaciones)} asignaciones creadas");
    }

    /**
     * Contar registros en array
     */
    private function contarRegistros($array): int
    {
        return is_array($array) ? count($array) : 0;
    }
}
