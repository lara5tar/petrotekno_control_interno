@extends('layouts.app')

@section('title', 'Documentos del Vehículo')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Documentos del Vehículo</h1>
                <p class="text-gray-600 mt-1">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->numero_economico }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('vehiculos.show', $vehiculo->id) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Vehículo
                </a>
                <button onclick="openCreateDocumentModal()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Agregar Documento
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($documentos->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Vencimiento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($documentos as $documento)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $documento->tipoDocumento->nombre_tipo_documento }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $documento->descripcion ?: 'Sin descripción' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($documento->fecha_vencimiento)
                                        <div class="text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Sin fecha</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $estatus = 'vigente';
                                        $claseEstatus = 'bg-green-100 text-green-800';
                                        
                                        if ($documento->fecha_vencimiento) {
                                            $fechaVencimiento = \Carbon\Carbon::parse($documento->fecha_vencimiento);
                                            $hoy = \Carbon\Carbon::now();
                                            $diasParaVencer = $hoy->diffInDays($fechaVencimiento, false);
                                            
                                            if ($diasParaVencer < 0) {
                                                $estatus = 'vencido';
                                                $claseEstatus = 'bg-red-100 text-red-800';
                                            } elseif ($diasParaVencer <= 30) {
                                                $estatus = 'por_vencer';
                                                $claseEstatus = 'bg-yellow-100 text-yellow-800';
                                            }
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $claseEstatus }}">
                                        @if($estatus === 'vigente')
                                            Vigente
                                        @elseif($estatus === 'por_vencer')
                                            Por Vencer
                                        @else
                                            Vencido
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($documento->ruta_archivo)
                                        <a href="{{ route('documentos.download', $documento->id) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm">
                                            <i class="fas fa-file-download mr-1"></i>Descargar
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">Sin archivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('documentos.show', $documento->id) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('documentos.edit', $documento->id) }}" 
                                           class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ $documento->id }}')" 
                                class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay documentos</h3>
                <p class="text-gray-500 mb-4">Este vehículo no tiene documentos registrados.</p>
                <button onclick="openCreateDocumentModal()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Agregar Primer Documento
                </button>
            </div>
        @endif
    </div>
</div>

<div id="createDocumentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Agregar Documento al Vehículo</h3>
                <button onclick="closeCreateDocumentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="createDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="modal_tipo_documento_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Documento <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo_documento_id" id="modal_tipo_documento_id" required 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar tipo</option>
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre_tipo_documento }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="modal_descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" id="modal_descripcion" rows="3" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Descripción del documento (opcional)"></textarea>
                    </div>

                    <div>
                        <label for="modal_fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" id="modal_fecha_vencimiento" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="modal_archivo" class="block text-sm font-medium text-gray-700 mb-2">
                            Archivo <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="archivo" id="modal_archivo" required 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt,.xls,.xlsx"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG, TXT, XLS, XLSX (máx. 10MB)</p>
                    </div>

                    <div>
                        <label for="modal_contenido" class="block text-sm font-medium text-gray-700 mb-2">Contenido JSON (Opcional)</label>
                        <textarea name="contenido" id="modal_contenido" rows="3" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder='{"campo": "valor", "otro_campo": "otro_valor"}'></textarea>
                        <p class="text-xs text-gray-500 mt-1">Formato JSON válido para datos adicionales</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCreateDocumentModal()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-save mr-2"></i>Guardar Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openCreateDocumentModal() {
    document.getElementById('createDocumentModal').classList.remove('hidden');
}

function closeCreateDocumentModal() {
    document.getElementById('createDocumentModal').classList.add('hidden');
    document.getElementById('createDocumentForm').reset();
}

function confirmDelete(documentoId) {
    if (confirm('¿Estás seguro de que deseas eliminar este documento?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/documentos/' + String(documentoId);
        
        var methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        var tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function showAlert(message, type) {
    alert(message);
}

document.getElementById('createDocumentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateDocumentModal();
    }
});
</script>
@endsection