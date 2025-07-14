@extends('layouts.app')

@section('title', 'Detalle de Vehículo')

@section('header', 'Control de Inventario de Vehículos')

@section('content')
    <div class="mb-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-petroyellow">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Inicio
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('vehiculos.index') }}" class="text-gray-700 hover:text-petroyellow ml-1 md:ml-2 text-sm font-medium">Gestionar Vehículos</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2 text-sm font-medium">Nissan NP300 [JDDJF]</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <!-- Datos Generales y Estado -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
            <!-- Columna 1-3: Datos Generales -->
            <div class="lg:col-span-3">
                <div class="border rounded-lg p-4 h-full">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Datos Generales</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Marca y Modelo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                            <input type="text" value="Nissan NP300" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                            <input type="text" value="2022" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                        </div>
                        
                        <!-- Identificador (VIN) y Placas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Identificador Único (VIN)</label>
                            <input type="text" value="3VWRTD4S66LJDDJF" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Placas</label>
                            <input type="text" value="XSG-323A" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                        </div>
                        
                        <!-- Póliza y Derecho Vehicular -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Póliza de Seguro</label>
                            <div class="flex">
                                <input type="text" value="8440057579" class="p-2 border border-gray-300 rounded-l-md w-full bg-gray-50" readonly>
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 rounded-r-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Derecho Vehicular</label>
                            <div class="flex">
                                <input type="text" value="2023" class="p-2 border border-gray-300 rounded-l-md w-full bg-gray-50" readonly>
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 rounded-r-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna 4: Información de estado -->
            <div>
                <div class="border rounded-lg p-4 h-full">
                    <div class="text-center mb-4">
                        <p class="text-sm font-medium text-gray-700">Último Kilometraje</p>
                        <p class="text-2xl font-bold text-gray-800">125,145</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-700 mb-2">Estatus Actual</p>
                        <select class="p-2 border border-gray-300 rounded-md w-full bg-amber-100 text-amber-800 font-medium">
                            <option selected>En Obra</option>
                            <option>Disponible</option>
                            <option>Mantenimiento</option>
                            <option>Reparación</option>
                            <option>Fuera de Servicio</option>
                        </select>
                        <button class="mt-2 w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">Cambiar Obra</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestañas de navegación -->
        <div class="border-b border-gray-200 mb-4">
            <nav class="flex -mb-px">
                <button class="py-2 px-4 border-b-2 border-petroyellow text-petroyellow font-medium">
                    Operación
                </button>
                <button class="py-2 px-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Documentos
                </button>
                <button class="py-2 px-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Mantenimientos
                </button>
            </nav>
        </div>

        <!-- Contenido de la pestaña Operación -->
        <div>
            <!-- Información de Obra -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Obra Actual</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Obra</label>
                        <input type="text" value="Libramiento Monterrey" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lugar</label>
                        <input type="text" value="Monterrey, N.L." class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asignación de Vehículos</label>
                        <input type="text" value="Titular de los agregados" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asignación de Personal</label>
                        <input type="text" value="Titular de los agregados" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inicio de Obra</label>
                        <input type="text" value="1/02/2023" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Término de Obra</label>
                        <input type="text" value="1/04/2024" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Km Inicial</label>
                        <input type="text" value="1" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Km Final</label>
                        <input type="text" value="200" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <input type="text" value="Juan Pérez" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                </div>
            </div>
            
            <!-- Información del Operador -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium text-gray-800">Operador Actual</h3>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-1 px-3 rounded text-sm">Cambiar Operador</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" value="Marco Alfredo" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                        <input type="text" value="Delgado Reyes" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NSS</label>
                        <input type="text" value="140726883" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Domicilio</label>
                        <input type="text" value="Monterrey, Nuevo León" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                        <input type="text" value="8344" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Identificación Electoral</label>
                        <div class="flex">
                            <input type="text" value="DEMBR20112384" class="p-2 border border-gray-300 rounded-l-md w-full bg-gray-50" readonly>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 rounded-r-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Licencia de Manejo</label>
                        <div class="flex">
                            <input type="text" value="1687" class="p-2 border border-gray-300 rounded-l-md w-full bg-gray-50" readonly>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 rounded-r-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inicio de Obra</label>
                        <input type="text" value="1/02/2023" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Término de Obra</label>
                        <input type="text" value="1/04/2024" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Km Inicial</label>
                        <input type="text" value="1" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Km Final</label>
                        <input type="text" value="200" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                </div>
            </div>
            
            <!-- Fotografía del vehículo -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Fotografía</h3>
                <div class="border rounded-lg overflow-hidden">
                    <img src="{{ asset('images/nissan_np300.jpg') }}" alt="Nissan NP300" class="w-full h-auto">
                </div>
            </div>
            
            <!-- Registro de Kilometraje -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium text-gray-800">Kilometrajes</h3>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-1 px-3 rounded text-sm">Capturar Nuevo</button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Captura</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obra</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario Captura</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">125,145</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">02/06/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Libramiento Monterrey</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Marco Delgado</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">124,356</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">28/05/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Libramiento Monterrey</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Marco Delgado</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">122,996</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">28/05/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Libramiento Monterrey</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Marco Delgado</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">120,514</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">28/05/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Libramiento Monterrey</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Diego Lopez</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">118,117</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15/04/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Carretera ABC</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">José Pérez</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-between">
        <a href="{{ route('vehiculos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Regresar
        </a>
        <button class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Guardar
        </button>
    </div>
@endsection