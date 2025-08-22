@php
    $notifications = \App\Services\NotificationService::getNotifications();
@endphp

{{-- Notificaciones principales del sistema --}}
@if(session('notification') || count($notifications) > 0)
    @foreach($notifications as $notification)
        @php
            $styles = \App\Services\NotificationService::getTypeStyles($notification['type']);
            $notificationId = 'notification-' . $notification['id'];
        @endphp
        
        <div class="mb-4 {{ $styles['bg'] }} border {{ $styles['border'] }} {{ $styles['text'] }} px-4 py-3 rounded-lg relative shadow-sm" 
             role="alert" 
             id="{{ $notificationId }}"
             x-data="{ show: true, progress: {{ $notification['data']['progress'] ?? 0 }} }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            
            <div class="flex items-start">
                {{-- Icono --}}
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 {{ $styles['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $styles['icon_path'] }}"/>
                    </svg>
                </div>
                
                {{-- Contenido --}}
                <div class="ml-3 flex-1">
                    {{-- Mensaje principal --}}
                    <div class="text-sm font-medium">
                        {!! $notification['message'] !!}
                    </div>
                    
                    {{-- Barra de progreso (si existe) --}}
                    @if(isset($notification['data']['progress']))
                        <div class="mt-2">
                            <div class="bg-white bg-opacity-30 rounded-full h-2">
                                <div class="bg-current h-2 rounded-full transition-all duration-300" 
                                     :style="`width: ${progress}%`"></div>
                            </div>
                            <div class="text-xs mt-1" x-text="`${progress}% completado`"></div>
                        </div>
                    @endif
                    
                    {{-- Acción (si existe) --}}
                    @if(isset($notification['data']['action']))
                        <div class="mt-2">
                            <a href="{{ $notification['data']['action']['url'] }}" 
                               class="text-sm underline hover:no-underline font-medium">
                                {{ $notification['data']['action']['text'] }}
                            </a>
                        </div>
                    @endif
                </div>
                
                {{-- Botón cerrar --}}
                <button type="button" 
                        class="ml-4 flex-shrink-0 {{ $styles['icon'] }} hover:opacity-75 transition-opacity"
                        @click="show = false">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endforeach
@endif

{{-- Mantener compatibilidad con sistema anterior --}}
@if(session('success') && !session('notification'))
    @php
        $containsPassword = str_contains(session('success'), 'Contraseña generada');
    @endphp
    
    @if($containsPassword)
        {{-- Alert especial para contraseñas generadas --}}
        <div class="mb-6 bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-400 text-green-800 px-6 py-4 rounded-lg shadow-lg relative" 
             role="alert" 
             x-data="{ show: true }"
             x-show="show"
             x-transition>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-bold text-green-800 mb-2">¡Usuario Creado Exitosamente!</h3>
                    <div class="text-sm">{!! session('success') !!}</div>
                </div>
                <button type="button" 
                        class="ml-4 flex-shrink-0 text-green-400 hover:text-green-600"
                        @click="show = false">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @else
        {{-- Alert normal para otros mensajes de éxito --}}
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" 
             role="alert"
             x-data="{ show: true }"
             x-show="show"
             x-transition>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{!! session('success') !!}</span>
                </div>
                <button type="button" 
                        class="ml-4 flex-shrink-0 text-green-500 hover:text-green-700"
                        @click="show = false">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif
@endif

@if(session('error') && !session('notification'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" 
         role="alert"
         x-data="{ show: true }"
         x-show="show"
         x-transition>
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <strong class="font-bold">¡Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            <button type="button" 
                    class="ml-4 flex-shrink-0 text-red-500 hover:text-red-700"
                    @click="show = false">
                <span class="sr-only">Cerrar</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </button>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" 
         role="alert"
         x-data="{ show: true }"
         x-show="show"
         x-transition>
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <strong class="font-bold">¡Advertencia!</strong>
                <span class="block sm:inline">{{ session('warning') }}</span>
            </div>
            <button type="button" 
                    class="ml-4 flex-shrink-0 text-yellow-500 hover:text-yellow-700"
                    @click="show = false">
                <span class="sr-only">Cerrar</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </button>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" 
         role="alert"
         x-data="{ show: true }"
         x-show="show"
         x-transition>
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <strong class="font-bold">Información:</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
            <button type="button" 
                    class="ml-4 flex-shrink-0 text-blue-500 hover:text-blue-700"
                    @click="show = false">
                <span class="sr-only">Cerrar</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </button>
        </div>
    </div>
@endif

{{-- Errores de validación --}}
@if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" 
         role="alert"
         x-data="{ show: true }"
         x-show="show"
         x-transition>
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <strong class="font-bold">¡Errores de validación!</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" 
                    class="ml-4 flex-shrink-0 text-red-500 hover:text-red-700"
                    @click="show = false">
                <span class="sr-only">Cerrar</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </button>
        </div>
    </div>
@endif

{{-- Container para notificaciones toast dinámicas --}}
<div id="toast-container" 
     class="fixed top-4 right-4 z-50 space-y-2"
     x-data="toastManager()"
     x-init="initToastManager()">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
             :class="getToastClasses(toast.type)"
             x-show="toast.show"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5" :class="getIconClasses(toast.type)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(toast.type)"/>
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium" :class="getTextClasses(toast.type)" x-text="toast.message"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex" x-show="toast.closable">
                        <button @click="removeToast(toast.id)" 
                                class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="getCloseButtonClasses(toast.type)">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        
        initToastManager() {
            // Escuchar eventos de toast globales
            window.addEventListener('show-toast', (event) => {
                this.addToast(event.detail);
            });
        },
        
        addToast(toastData) {
            const toast = {
                id: Date.now() + Math.random(),
                type: toastData.type || 'info',
                message: toastData.message,
                closable: toastData.closable !== false,
                duration: toastData.duration || 5000,
                show: true
            };
            
            this.toasts.push(toast);
            
            if (toast.duration > 0) {
                setTimeout(() => {
                    this.removeToast(toast.id);
                }, toast.duration);
            }
        },
        
        removeToast(toastId) {
            const index = this.toasts.findIndex(toast => toast.id === toastId);
            if (index > -1) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 300);
            }
        },
        
        getToastClasses(type) {
            const classes = {
                success: 'bg-green-50 border border-green-200',
                error: 'bg-red-50 border border-red-200',
                warning: 'bg-yellow-50 border border-yellow-200',
                info: 'bg-blue-50 border border-blue-200'
            };
            return classes[type] || classes.info;
        },
        
        getIconClasses(type) {
            const classes = {
                success: 'text-green-400',
                error: 'text-red-400',
                warning: 'text-yellow-400',
                info: 'text-blue-400'
            };
            return classes[type] || classes.info;
        },
        
        getTextClasses(type) {
            const classes = {
                success: 'text-green-800',
                error: 'text-red-800',
                warning: 'text-yellow-800',
                info: 'text-blue-800'
            };
            return classes[type] || classes.info;
        },
        
        getCloseButtonClasses(type) {
            const classes = {
                success: 'text-green-400 hover:text-green-500 focus:ring-green-500',
                error: 'text-red-400 hover:text-red-500 focus:ring-red-500',
                warning: 'text-yellow-400 hover:text-yellow-500 focus:ring-yellow-500',
                info: 'text-blue-400 hover:text-blue-500 focus:ring-blue-500'
            };
            return classes[type] || classes.info;
        },
        
        getIconPath(type) {
            const paths = {
                success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z',
                info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            };
            return paths[type] || paths.info;
        }
    }
}

// Función global para mostrar toast
window.showToast = function(type, message, options = {}) {
    window.dispatchEvent(new CustomEvent('show-toast', {
        detail: {
            type: type,
            message: message,
            ...options
        }
    }));
};
</script>
