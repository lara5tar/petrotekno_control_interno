<!-- resources/views/components/navbar.blade.php -->
<div class="bg-petrodark text-white p-4 flex justify-between items-center">
    <div class="flex items-center">
        <button class="md:hidden mr-4" id="menu-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-xl font-medium">{{ $title ?? 'Dashboard' }}</h1>
    </div>
    <div class="flex items-center">
        <span class="text-petroyellow mr-4">{{ Auth::user()->name ?? 'Usuario' }}</span>
        <span class="text-sm text-gray-400">v1.0</span>
    </div>
</div>