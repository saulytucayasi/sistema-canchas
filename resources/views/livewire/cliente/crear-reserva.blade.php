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
            <!-- Paso 1: Selección de Cliente (solo visible para admin/secretaria) -->
            @if(!$isClienteUser)
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
            @endif

            <!-- Paso {{ $isClienteUser ? '1' : '2' }}: Selección de Cancha -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">{{ $isClienteUser ? '1' : '2' }}</span>
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

            <!-- Paso {{ $isClienteUser ? '2' : '3' }}: Selección de Fecha -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">{{ $isClienteUser ? '2' : '3' }}</span>
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

            <!-- Paso {{ $isClienteUser ? '3' : '4' }}: Selección de Horario -->
            @if($selectedCancha && $fecha)
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">{{ $isClienteUser ? '3' : '4' }}</span>
                    Seleccionar Horario
                </h3>
                
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800">Selección múltiple</h4>
                            <p class="text-sm text-blue-700 mt-1">Puedes seleccionar múltiples horarios. Haz clic en los horarios que desees reservar.</p>
                        </div>
                    </div>
                </div>

                @if(count($horariosDisponibles) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                        @foreach($horariosDisponibles as $horario)
                        @php
                            $horarioKey = $horario['hora_inicio'] . '-' . $horario['hora_fin'];
                            $isSelected = in_array($horarioKey, $horarios_seleccionados);
                        @endphp
                        <button type="button" 
                                wire:click="toggleHorario({{ json_encode($horario) }})"
                                class="p-3 rounded-lg border-2 text-sm font-medium transition-all duration-200
                                       {{ $horario['disponible'] 
                                          ? ($isSelected 
                                             ? 'border-orange-500 bg-orange-50 text-orange-700' 
                                             : 'border-green-300 bg-green-50 text-green-700 hover:border-green-400 hover:bg-green-100') 
                                          : 'border-red-300 bg-red-50 text-red-700 cursor-not-allowed' }}"
                                {{ !$horario['disponible'] ? 'disabled' : '' }}>
                            <div class="flex items-center justify-between">
                                <span>{{ $horario['hora_inicio'] }} - {{ $horario['hora_fin'] }}</span>
                                @if($horario['disponible'])
                                    @if($isSelected)
                                        <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1 4a1 1 0 01-1-1v-4a1 1 0 012 0v4a1 1 0 01-1 1zm4-4a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
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

                    @if(count($horarios_seleccionados) > 0 && $precio_total > 0)
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h4 class="font-semibold text-orange-800 mb-2">Resumen de la Reserva</h4>
                        <div class="space-y-2 text-sm text-orange-700">
                            <p><strong>Horarios seleccionados ({{ count($horarios_seleccionados) }}):</strong></p>
                            <div class="grid grid-cols-2 gap-1 ml-4">
                                @foreach($horarios_seleccionados as $horario)
                                    <span class="text-xs bg-orange-100 px-2 py-1 rounded">{{ str_replace('-', ' a ', $horario) }}</span>
                                @endforeach
                            </div>
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
                @error('horarios_seleccionados') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            @endif

            <!-- Paso {{ $isClienteUser ? '4' : '5' }}: Voucher de Pago -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">{{ $isClienteUser ? '4' : '5' }}</span>
                    Voucher de Pago (Obligatorio)
                </h3>
                <div class="space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-red-800">Importante</h4>
                                <p class="text-sm text-red-700 mt-1">Debes subir una imagen del voucher de pago antes de crear la reserva. El administrador o secretario verificará el pago antes de confirmar tu reserva.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Imagen del Voucher de Pago *
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-red-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="voucher-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                        <span>Subir imagen</span>
                                        <input id="voucher-upload" wire:model="voucher_pago" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">o arrastra y suelta</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 2MB</p>
                            </div>
                        </div>
                        @error('voucher_pago') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        
                        @if($voucher_pago)
                            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm text-green-700">Imagen cargada correctamente</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Paso {{ $isClienteUser ? '5' : '6' }}: Observaciones -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">{{ $isClienteUser ? '5' : '6' }}</span>
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
                        {{ !$selectedCancha || !$fecha || count($horarios_seleccionados) === 0 || !$cliente_id || !$voucher_pago ? 'disabled' : '' }}>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Crear Reserva
                </button>
            </div>
        </form>
    </div>
</div>
