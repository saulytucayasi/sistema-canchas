<div class="p-6">
    <!-- Header con botón crear -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Gestión de Canchas</h3>
            <p class="text-sm text-gray-600">Administra las canchas deportivas</p>
        </div>
        <button wire:click="openCreateModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nueva Cancha
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

    <!-- Barra de búsqueda -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" wire:model.live="search" placeholder="Buscar canchas..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Grid de canchas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($canchas as $cancha)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <!-- Imagen de la cancha -->
            <div class="h-48 bg-gray-200 relative">
                @if($cancha->imagenes->first())
                    <img src="{{ asset('storage/' . $cancha->imagenes->first()->ruta_imagen) }}" 
                         alt="{{ $cancha->nombre }}" 
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                @endif
                
                <!-- Estado badge -->
                <div class="absolute top-2 right-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cancha->activa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $cancha->activa ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>

            <!-- Información de la cancha -->
            <div class="p-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ $cancha->nombre }}</h4>
                <p class="text-sm text-gray-600 mb-2">{{ ucfirst($cancha->tipo) }}</p>
                
                <div class="space-y-1 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Capacidad: {{ $cancha->capacidad }} personas
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        S/ {{ number_format($cancha->precio_por_hora, 2, '.', ',') }}/hora
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $cancha->hora_apertura }} - {{ $cancha->hora_cierre }}
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex space-x-2">
                    <button wire:click="openEditModal({{ $cancha->id }})" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                        Editar
                    </button>
                    <button wire:click="toggleEstado({{ $cancha->id }})" 
                            class="flex-1 {{ $cancha->activa ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-2 rounded text-sm">
                        {{ $cancha->activa ? 'Desactivar' : 'Activar' }}
                    </button>
                    <button wire:click="deleteCancha({{ $cancha->id }})" 
                            wire:confirm="¿Estás seguro de eliminar esta cancha?"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginación -->
    <div class="mt-8">
        {{ $canchas->links() }}
    </div>

    <!-- Modal Crear/Editar Cancha -->
    @if($showCreateModal || $showEditModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $showCreateModal ? 'Crear Nueva Cancha' : 'Editar Cancha' }}
                </h3>
                <button wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" 
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="{{ $showCreateModal ? 'createCancha' : 'updateCancha' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" wire:model="nombre" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <select wire:model="tipo" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Seleccionar tipo...</option>
                            @foreach($tiposDisponibles as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('tipo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio por Hora</label>
                        <input type="number" wire:model="precio_por_hora" step="0.01" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('precio_por_hora') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacidad</label>
                        <input type="number" wire:model="capacidad" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('capacidad') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora Apertura</label>
                        <input type="time" wire:model="hora_apertura" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('hora_apertura') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora Cierre</label>
                        <input type="time" wire:model="hora_cierre" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('hora_cierre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea wire:model="descripcion" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"></textarea>
                        @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Imágenes</label>
                        <input type="file" wire:model="imagenes" multiple accept="image/*" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('imagenes.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-sm text-gray-500 mt-1">Máximo 2MB por imagen</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="activa" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Cancha activa</span>
                        </label>
                    </div>
                </div>

                <!-- Gestión de Horarios -->
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-medium text-gray-700">Horarios de Funcionamiento</h4>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="mostrarHorarios" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Configurar horarios específicos</span>
                        </label>
                    </div>

                    @if($mostrarHorarios)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <p class="text-sm text-gray-600">Define los horarios específicos disponibles para esta cancha</p>
                            <button type="button" wire:click="generarHorarios" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                Generar Automáticamente
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach($horarios_disponibles as $index => $horario)
                            <div class="flex items-center space-x-3 bg-white p-3 rounded border">
                                <div class="flex-1 grid grid-cols-2 gap-3">
                                    <div>
                                        <input type="time" wire:model="horarios_disponibles.{{ $index }}.hora_inicio" 
                                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <input type="time" wire:model="horarios_disponibles.{{ $index }}.hora_fin" 
                                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" wire:click="toggleHorario({{ $index }})" 
                                            class="px-2 py-1 rounded text-xs font-medium {{ $horario['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $horario['activo'] ? 'Activo' : 'Inactivo' }}
                                    </button>
                                    <button type="button" wire:click="eliminarHorario({{ $index }})" 
                                            class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach

                            <button type="button" wire:click="agregarHorario" 
                                    class="w-full border-2 border-dashed border-gray-300 rounded-lg p-3 text-gray-600 hover:border-gray-400 hover:text-gray-700">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agregar Horario
                            </button>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-500 bg-gray-50 p-4 rounded-lg">
                        <strong>Horarios automáticos:</strong> Se generarán horarios de 1 hora desde la hora de apertura hasta la hora de cierre.
                    </p>
                    @endif
                </div>

                <!-- Imágenes existentes (solo en modo edición) -->
                @if($showEditModal && $selectedCancha && $selectedCancha->imagenes->count() > 0)
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Imágenes actuales</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($selectedCancha->imagenes as $imagen)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $imagen->ruta_imagen) }}" 
                                 alt="Imagen de cancha" 
                                 class="w-full h-24 object-cover rounded-lg">
                            <button type="button" 
                                    wire:click="eliminarImagen({{ $imagen->id }})"
                                    wire:confirm="¿Eliminar esta imagen?"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" 
                            wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        {{ $showCreateModal ? 'Crear Cancha' : 'Actualizar Cancha' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
