<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Crear Nueva Reserva</h2>
            <p class="text-gray-600">Selecciona una cancha, fecha y horario para tu reserva</p>
        </div>

        <!-- Mensajes de estado -->
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="crearReserva" class="space-y-8">
            <!-- Paso 1: Selección de Cliente -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">1</span>
                    Información del Cliente
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                        <select wire:model="cliente_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }} - {{ $cliente->documento }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Paso 2: Selección de Cancha -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                    Seleccionar Cancha
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($canchasDisponibles as $cancha)
                    <div class="border-2 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md {{ $selectedCancha == $cancha->id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}"
                         wire:click="$set('selectedCancha', {{ $cancha->id }})">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">{{ $cancha->nombre }}</h4>
                            @if($selectedCancha == $cancha->id)
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ ucfirst($cancha->tipo) }}</p>
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $cancha->capacidad }} personas
                        </div>
                        <div class="flex items-center text-sm font-semibold text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            S/ {{ number_format($cancha->precio_por_hora, 2, '.', ',') }}/hora
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('selectedCancha') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Paso 3: Selección de Fecha -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">3</span>
                    Seleccionar Fecha
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de la reserva</label>
                        <input type="date" wire:model.live="fecha" min="{{ date('Y-m-d') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        @error('fecha') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Paso 4: Selección de Horario -->
            @if($selectedCancha && $fecha)
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">4</span>
                    Seleccionar Horario
                </h3>
                
                @if(count($horariosDisponibles) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                        @foreach($horariosDisponibles as $horario)
                        <button type="button" 
                                wire:click="seleccionarHorario({{ json_encode($horario) }})"
                                class="p-3 rounded-lg border-2 text-sm font-medium transition-all duration-200
                                       {{ $horario['disponible'] 
                                          ? ($horario_seleccionado === $horario['hora_inicio'].'-'.$horario['hora_fin'] 
                                             ? 'border-orange-500 bg-orange-50 text-orange-700' 
                                             : 'border-green-300 bg-green-50 text-green-700 hover:border-green-400 hover:bg-green-100') 
                                          : 'border-red-300 bg-red-50 text-red-700 cursor-not-allowed' }}"
                                {{ !$horario['disponible'] ? 'disabled' : '' }}>
                            <div class="flex items-center justify-between">
                                <span>{{ $horario['hora_inicio'] }} - {{ $horario['hora_fin'] }}</span>
                                @if($horario['disponible'])
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                            @if(!$horario['disponible'] && $horario['motivo'])
                            <div class="text-xs text-red-600 mt-1">{{ $horario['motivo'] }}</div>
                            @endif
                        </button>
                        @endforeach
                    </div>

                    @if($horario_seleccionado && $precio_total > 0)
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h4 class="font-semibold text-orange-800 mb-2">Resumen de la Reserva</h4>
                        <div class="space-y-1 text-sm text-orange-700">
                            <p><strong>Horario:</strong> {{ str_replace('-', ' a ', $horario_seleccionado) }}</p>
                            <p><strong>Precio Total:</strong> S/ {{ number_format($precio_total, 2, '.', ',') }}</p>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>No hay horarios disponibles para esta fecha</p>
                    </div>
                @endif
                @error('hora_inicio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @error('hora_fin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            @endif

            <!-- Paso 5: Observaciones -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">5</span>
                    Observaciones (Opcional)
                </h3>
                <div>
                    <textarea wire:model="observaciones" rows="3" 
                              placeholder="Agregar cualquier observación o requerimiento especial..."
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    @error('observaciones') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('cliente.dashboard') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-md transition duration-200 flex items-center"
                        {{ !$selectedCancha || !$fecha || !$horario_seleccionado || !$cliente_id ? 'disabled' : '' }}>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Crear Reserva
                </button>
            </div>
        </form>
    </div>
</div>
