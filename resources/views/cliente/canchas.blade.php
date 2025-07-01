<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Canchas Disponibles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Conoce Nuestras Canchas</h3>
                    
                    <!-- Grid de canchas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach(\App\Models\Cancha::where('activa', true)->with(['imagenes' => function($query) { $query->where('es_principal', true); }])->get() as $cancha)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border hover:shadow-lg transition-shadow duration-300">
                            <!-- Imagen de la cancha -->
                            <div class="h-48 bg-gray-200 relative">
                                @if($cancha->imagenes->first())
                                    <img src="{{ asset('storage/' . $cancha->imagenes->first()->ruta_imagen) }}" 
                                         alt="{{ $cancha->nombre }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-green-100 to-green-200">
                                        <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Badge del tipo de cancha -->
                                <div class="absolute top-3 left-3">
                                    <span class="px-3 py-1 bg-green-600 text-white text-xs font-semibold rounded-full">
                                        {{ ucfirst($cancha->tipo) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Información de la cancha -->
                            <div class="p-5">
                                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $cancha->nombre }}</h4>
                                
                                @if($cancha->descripcion)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $cancha->descripcion }}</p>
                                @endif
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span><strong>Capacidad:</strong> {{ $cancha->capacidad }} personas</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        <span class="text-lg font-semibold text-green-600">S/ {{ number_format($cancha->precio_por_hora, 2, '.', ',') }}/hora</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span><strong>Horario:</strong> {{ $cancha->hora_apertura }} - {{ $cancha->hora_cierre }}</span>
                                    </div>
                                </div>

                                <!-- Botón de reservar -->
                                <div class="mt-4">
                                    <a href="{{ route('cliente.crear-reserva', ['cancha_id' => $cancha->id]) }}" 
                                       class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Reservar Ahora
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if(\App\Models\Cancha::where('activa', true)->count() === 0)
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay canchas disponibles</h3>
                        <p class="text-gray-500">Actualmente no tenemos canchas disponibles para reservar.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>