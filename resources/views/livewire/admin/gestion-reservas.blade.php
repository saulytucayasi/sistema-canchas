<div class="p-6">
    <!-- Header con botón crear -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Gestión de Reservas</h3>
            <p class="text-sm text-gray-600">Administra todas las reservas del sistema</p>
        </div>
        <button wire:click="openCreateModal" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nueva Reserva
        </button>
    </div>

    <!-- Mensajes de estado -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Búsqueda -->
        <div class="relative">
            <input type="text" wire:model.live="search" placeholder="Buscar por cliente o cancha..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Filtro por estado -->
        <div>
            <select wire:model.live="filtroEstado" class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                @foreach($estadosDisponibles as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filtro por fecha -->
        <div>
            <input type="date" wire:model.live="filtroFecha" 
                   class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
        </div>
    </div>

    <!-- Tabla de reservas -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cancha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voucher</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reservas as $reserva)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-orange-800">{{ substr($reserva->cliente->nombre, 0, 1) }}{{ substr($reserva->cliente->apellido, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</div>
                                    <div class="text-sm text-gray-500">{{ $reserva->cliente->telefono }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $reserva->cancha->nombre }}</div>
                            <div class="text-sm text-gray-500">{{ ucfirst($reserva->cancha->tipo) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $reserva->fecha->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($reserva->estado === 'confirmada') bg-green-100 text-green-800 
                                @elseif($reserva->estado === 'pendiente') bg-yellow-100 text-yellow-800 
                                @elseif($reserva->estado === 'completada') bg-blue-100 text-blue-800 
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($reserva->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reserva->voucher_pago)
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($reserva->estado_voucher === 'verificado') bg-green-100 text-green-800 
                                        @elseif($reserva->estado_voucher === 'rechazado') bg-red-100 text-red-800 
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($reserva->estado_voucher) }}
                                    </span>
                                    <button onclick="window.open('{{ asset('storage/' . $reserva->voucher_pago) }}', '_blank')" 
                                            class="text-blue-600 hover:text-blue-900 text-xs">
                                        Ver
                                    </button>
                                    @if($reserva->estado_voucher === 'pendiente')
                                        <button wire:click="verificarVoucher({{ $reserva->id }}, 'verificado')" 
                                                class="text-green-600 hover:text-green-900 text-xs">
                                            Aprobar
                                        </button>
                                        <button wire:click="verificarVoucher({{ $reserva->id }}, 'rechazado')" 
                                                class="text-red-600 hover:text-red-900 text-xs">
                                            Rechazar
                                        </button>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">Sin voucher</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            S/ {{ number_format($reserva->precio_total, 2, '.', ',') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button wire:click="openEditModal({{ $reserva->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>

                                @if($reserva->estado === 'pendiente')
                                <button wire:click="cambiarEstado({{ $reserva->id }}, 'confirmada')" 
                                        class="text-green-600 hover:text-green-900" title="Confirmar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                @endif

                                @if($reserva->estado === 'confirmada')
                                <button wire:click="cambiarEstado({{ $reserva->id }}, 'completada')" 
                                        class="text-blue-600 hover:text-blue-900" title="Completar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                @endif

                                @if($reserva->estado !== 'cancelada' && $reserva->estado !== 'completada')
                                <button wire:click="cambiarEstado({{ $reserva->id }}, 'cancelada')" 
                                        wire:confirm="¿Estás seguro de cancelar esta reserva?"
                                        class="text-red-600 hover:text-red-900" title="Cancelar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                @endif

                                <button wire:click="deleteReserva({{ $reserva->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar esta reserva?"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron reservas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $reservas->links() }}
    </div>

    <!-- Modal Crear/Editar Reserva -->
    @if($showCreateModal || $showEditModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $showCreateModal ? 'Crear Nueva Reserva' : 'Editar Reserva' }}
                </h3>
                <button wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" 
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="{{ $showCreateModal ? 'createReserva' : 'updateReserva' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select wire:model="cliente_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cancha</label>
                        <select wire:model.live="cancha_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Seleccionar cancha...</option>
                            @foreach($canchas as $cancha)
                            <option value="{{ $cancha->id }}">{{ $cancha->nombre }} - {{ ucfirst($cancha->tipo) }}</option>
                            @endforeach
                        </select>
                        @error('cancha_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input type="date" wire:model.live="fecha" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                        @error('fecha') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <select wire:model="estado" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            @foreach($estadosDisponibles as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('estado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Horarios Disponibles</label>
                        @if($cancha_id && $fecha && count($horariosDisponibles) > 0)
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($horariosDisponibles as $horario)
                                <button type="button" 
                                        wire:click="seleccionarHorario({{ json_encode($horario) }})"
                                        class="p-3 rounded-lg border-2 text-sm font-medium transition-all duration-200
                                               {{ $horario['disponible'] 
                                                  ? ($horario_seleccionado === $horario['hora_inicio'].'-'.$horario['hora_fin'] 
                                                     ? 'border-orange-500 bg-orange-50 text-orange-700' 
                                                     : 'border-green-300 bg-green-50 text-green-700 hover:border-green-400') 
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
                            @if($horario_seleccionado)
                            <div class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                <p class="text-sm text-orange-700">
                                    <strong>Horario seleccionado:</strong> {{ str_replace('-', ' a ', $horario_seleccionado) }}
                                </p>
                            </div>
                            @endif
                        @elseif($cancha_id && $fecha)
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p>No hay horarios disponibles para esta fecha</p>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p>Selecciona una cancha y fecha para ver horarios disponibles</p>
                            </div>
                        @endif
                        @error('hora_inicio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @error('hora_fin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio Total</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">S/</span>
                            </div>
                            <input type="number" wire:model="precio_total" 
                                   class="pl-8 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="0.00">
                        </div>
                        @error('precio_total') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500">El precio se calcula automáticamente al seleccionar un horario</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea wire:model="observaciones" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"></textarea>
                        @error('observaciones') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" 
                            wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        {{ $showCreateModal ? 'Crear Reserva' : 'Actualizar Reserva' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
