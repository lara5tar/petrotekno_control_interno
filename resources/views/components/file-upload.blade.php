@props([
    'name' => '',
    'label' => 'Archivo',
    'accept' => '.pdf,.jpg,.jpeg,.png',
    'maxSize' => '10MB',
    'required' => false,
    'existingFile' => null,
    'existingFileLabel' => 'Ver archivo actual',
    'changeText' => 'Cambiar archivo',
    'uploadText' => 'Clic para subir o arrastrar y soltar',
    'description' => null,
    'helpText' => null
])

@php
    $inputId = 'file_' . $name . '_' . rand(1000, 9999);
    $statusId = $inputId . '_status';
    $hasExisting = !empty($existingFile);
@endphp

<div class="file-upload-component" x-data="fileUploadComponent('{{ $name }}', '{{ $maxSize }}', '{{ $accept }}')">
    {{-- Label del campo --}}
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    {{-- Descripción del campo --}}
    @if($description)
        <p class="text-sm text-gray-600 mb-2">{{ $description }}</p>
    @endif

    {{-- Mostrar archivo existente --}}
    @if($hasExisting)
        <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md">
            <div class="flex items-center justify-between">
                <span class="text-sm text-green-700 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    ✓ Archivo actual subido
                </span>
                <a href="{{ $existingFile }}" target="_blank" 
                   class="text-blue-600 hover:text-blue-800 text-sm underline flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    {{ $existingFileLabel }}
                </a>
            </div>
        </div>
    @endif

    {{-- Input de archivo (oculto) --}}
    <input type="file" 
           id="{{ $inputId }}" 
           name="{{ $name }}" 
           accept="{{ $accept }}" 
           class="hidden" 
           x-ref="fileInput"
           @change="handleFileChange($event)"
           {{ $required ? 'required' : '' }}
           {{ $attributes->except(['class', 'id', 'name', 'accept', 'required']) }}>

    {{-- Área de drop/click --}}
    <label for="{{ $inputId }}" 
           class="file-upload-area cursor-pointer w-full p-4 border-2 border-dashed rounded-lg text-center transition-all duration-200"
           :class="{
               'border-gray-300 hover:border-petroyellow bg-white hover:bg-gray-50': !isDragOver && !hasFile,
               'border-petroyellow bg-yellow-50': isDragOver,
               'border-green-400 bg-green-50': hasFile && !hasError,
               'border-red-400 bg-red-50': hasError
           }"
           @dragover.prevent="isDragOver = true"
           @dragleave.prevent="isDragOver = false"
           @drop.prevent="handleDrop($event)">
        
        <div class="flex flex-col items-center">
            {{-- Icono --}}
            <svg class="h-8 w-8 mb-2 transition-colors" 
                 :class="{
                     'text-gray-400': !isDragOver && !hasFile && !hasError,
                     'text-petroyellow': isDragOver,
                     'text-green-500': hasFile && !hasError,
                     'text-red-500': hasError
                 }" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            
            {{-- Texto principal --}}
            <span class="text-base font-medium mb-1" 
                  :class="{
                      'text-gray-700': !hasFile && !hasError,
                      'text-green-700': hasFile && !hasError,
                      'text-red-700': hasError
                  }">
                <span x-show="!hasFile">{{ $hasExisting ? $changeText : $uploadText }}</span>
                <span x-show="hasFile && !hasError" x-text="fileName"></span>
                <span x-show="hasError">Error en archivo</span>
            </span>
            
            {{-- Información del archivo --}}
            <div x-show="hasFile && !hasError" class="text-sm text-green-600">
                <span x-text="fileSize"></span>
            </div>
        </div>
    </label>

    {{-- Texto de ayuda --}}
    <div class="mt-2 text-center">
        <p class="text-xs text-gray-500" x-show="!hasFile && !hasError">
            @if($helpText)
                {{ $helpText }}
            @else
                {{ $accept }} (máx. {{ $maxSize }})
            @endif
        </p>
        
        {{-- Mensaje de estado --}}
        <p class="text-sm mt-1" 
           :class="{
               'text-green-600': hasFile && !hasError,
               'text-red-600': hasError
           }"
           x-show="statusMessage"
           x-text="statusMessage">
        </p>
    </div>

    {{-- Botón para limpiar (solo si hay archivo seleccionado) --}}
    <div x-show="hasFile" class="mt-2 text-center">
        <button type="button" 
                @click="clearFile()"
                class="text-sm text-gray-500 hover:text-gray-700 underline">
            Quitar archivo seleccionado
        </button>
    </div>

    {{-- Error de validación de Laravel --}}
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
function fileUploadComponent(fieldName, maxSize, acceptTypes) {
    return {
        isDragOver: false,
        hasFile: false,
        hasError: false,
        fileName: '',
        fileSize: '',
        statusMessage: '',
        
        handleFileChange(event) {
            const file = event.target.files[0];
            this.processFile(file);
        },
        
        handleDrop(event) {
            this.isDragOver = false;
            const file = event.dataTransfer.files[0];
            if (file) {
                // Actualizar el input con el archivo
                const input = this.$refs.fileInput;
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                
                this.processFile(file);
            }
        },
        
        processFile(file) {
            if (!file) {
                this.clearFile();
                return;
            }
            
            // Reset estado
            this.hasError = false;
            this.statusMessage = '';
            
            // Validar tamaño
            const maxSizeBytes = this.parseSize(maxSize);
            if (file.size > maxSizeBytes) {
                this.hasError = true;
                this.statusMessage = `El archivo es demasiado grande. Máximo ${maxSize}.`;
                this.clearFile();
                return;
            }
            
            // Validar tipo
            if (!this.isValidFileType(file, acceptTypes)) {
                this.hasError = true;
                this.statusMessage = `Formato no permitido. Tipos válidos: ${acceptTypes}`;
                this.clearFile();
                return;
            }
            
            // Archivo válido
            this.hasFile = true;
            this.fileName = file.name;
            this.fileSize = `(${this.formatFileSize(file.size)})`;
            this.statusMessage = `✅ Archivo seleccionado correctamente`;
        },
        
        clearFile() {
            this.hasFile = false;
            this.hasError = false;
            this.fileName = '';
            this.fileSize = '';
            this.statusMessage = '';
            this.$refs.fileInput.value = '';
        },
        
        parseSize(sizeStr) {
            const size = parseFloat(sizeStr);
            if (sizeStr.includes('MB')) return size * 1024 * 1024;
            if (sizeStr.includes('KB')) return size * 1024;
            return size;
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        isValidFileType(file, acceptTypes) {
            const extensions = acceptTypes.split(',').map(ext => ext.trim().toLowerCase());
            const fileName = file.name.toLowerCase();
            const fileType = file.type.toLowerCase();
            
            return extensions.some(ext => {
                if (ext.startsWith('.')) {
                    return fileName.endsWith(ext);
                }
                return fileType.includes(ext);
            });
        }
    }
}
</script>
@endpush
