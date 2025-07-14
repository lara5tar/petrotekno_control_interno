<!-- resources/views/components/sidebar.blade.php -->
<div class="bg-petroyellow w-full md:w-64 md:min-h-screen flex flex-col">
    <div class="text-center px-5 p-10">
        <h1 class="text-3xl font-bold text-petrodark">PETROTEKNO</h1>
    </div>
    
    <div class="">
        <h2 class="text-xl font-bold text-white text-center mb-4">Menú Principal</h2>
        <nav>
            <x-sidebar-item 
                route="{{ route('home') }}" 
                :active="request()->routeIs('home')" 
                icon='<path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />'
                label="Inicio" />

            <x-sidebar-item 
                route="{{ route('vehiculos.index') }}" 
                :active="request()->routeIs('vehiculos.*')" 
                icon='<path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H11a1 1 0 001-1v-1h3.05a2.5 2.5 0 014.9 0H20a1 1 0 001-1V8a1 1 0 00-.293-.707l-2-2A1 1 0 0018 5h-3.05a2.5 2.5 0 01-4.9 0H7a1 1 0 00-1 1v1.05a2.5 2.5 0 010 4.9V11a1 1 0 00-1-1H4a1 1 0 00-1 1v5a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H11a1 1 0 001-1v-1h3.05a2.5 2.5 0 014.9 0H20a1 1 0 001-1V8a1 1 0 00-.293-.707l-2-2A1 1 0 0018 5h-3.05a2.5 2.5 0 01-4.9 0H7a1 1 0 00-1 1z" />'
                label="Gestionar Vehículos" />

            <x-sidebar-item 
                route="#" 
                :active="request()->routeIs('personal.*')" 
                icon='<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />'
                label="Personal" />

            <x-sidebar-item 
                route="#" 
                :active="request()->routeIs('reportes.*')" 
                icon='<path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd" />'
                label="Reportes" />

            <x-sidebar-item 
                route="#" 
                :active="request()->routeIs('configuracion.*')" 
                icon='<path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />'
                label="Configuración" />
        </nav>
    </div>
    
    <div class="mt-auto mb-4">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block py-2.5 px-4 transition duration-200 text-gray-700 font-medium">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-6 0a1 1 0 10-2 0v4a1 1 0 102 0V7zm3 1a1 1 0 10-2 0v3a1 1 0 102 0V8z" clip-rule="evenodd" />
                </svg>
                Cerrar Sesión
            </div>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>