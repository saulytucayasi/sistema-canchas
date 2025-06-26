<?php

namespace App\Livewire\Cliente;

use App\Models\Reserva;
use Livewire\Component;
use Livewire\WithPagination;

class MisReservas extends Component
{
    use WithPagination;

    public $filtroEstado = '';
    public $search = '';
    public $showEditModal = false;
    public $selectedReserva = null;

    // Estados disponibles para cliente
    public $estadosDisponibles = [
        '' => 'Todas',
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'cancelada' => 'Cancelada',
        'completada' => 'Completada'
    ];

    // Formulario de edición (limitado)
    public $observaciones = '';

    protected $rules = [
        'observaciones' => 'nullable|string|max:500'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function openEditModal($reservaId)
    {
        $this->selectedReserva = Reserva::with(['cancha', 'cliente'])->find($reservaId);
        
        // Solo permitir editar reservas propias
        if ($this->selectedReserva->user_id !== auth()->id()) {
            session()->flash('error', 'No tienes permisos para editar esta reserva.');
            return;
        }

        // Solo permitir editar reservas pendientes o confirmadas que sean futuras
        if (!in_array($this->selectedReserva->estado, ['pendiente', 'confirmada']) || 
            $this->selectedReserva->fecha->isPast()) {
            session()->flash('error', 'No puedes editar esta reserva.');
            return;
        }

        $this->observaciones = $this->selectedReserva->observaciones;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateReserva()
    {
        $this->validate();

        $this->selectedReserva->update([
            'observaciones' => $this->observaciones
        ]);

        session()->flash('message', 'Reserva actualizada exitosamente.');
        $this->closeEditModal();
    }

    public function cancelarReserva($reservaId)
    {
        $reserva = Reserva::find($reservaId);
        
        // Verificar que es del usuario actual
        if ($reserva->user_id !== auth()->id()) {
            session()->flash('error', 'No tienes permisos para cancelar esta reserva.');
            return;
        }

        // Solo permitir cancelar reservas futuras
        if ($reserva->fecha->isPast()) {
            session()->flash('error', 'No puedes cancelar una reserva pasada.');
            return;
        }

        // Solo permitir cancelar reservas pendientes o confirmadas
        if (!in_array($reserva->estado, ['pendiente', 'confirmada'])) {
            session()->flash('error', 'Esta reserva no se puede cancelar.');
            return;
        }

        $reserva->update(['estado' => 'cancelada']);
        session()->flash('message', 'Reserva cancelada exitosamente.');
    }

    private function resetForm()
    {
        $this->observaciones = '';
        $this->selectedReserva = null;
    }

    public function render()
    {
        $query = Reserva::with(['cancha', 'cliente'])
            ->where('user_id', auth()->id());

        // Filtro por búsqueda (cancha)
        if ($this->search) {
            $query->whereHas('cancha', function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por estado
        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        $reservas = $query->orderBy('fecha', 'desc')
                         ->orderBy('hora_inicio', 'desc')
                         ->paginate(10);

        // Estadísticas
        $estadisticas = [
            'total' => Reserva::where('user_id', auth()->id())->count(),
            'pendientes' => Reserva::where('user_id', auth()->id())->where('estado', 'pendiente')->count(),
            'confirmadas' => Reserva::where('user_id', auth()->id())->where('estado', 'confirmada')->count(),
            'proximas' => Reserva::where('user_id', auth()->id())
                ->where('fecha', '>=', today())
                ->where('estado', '!=', 'cancelada')
                ->count()
        ];

        return view('livewire.cliente.mis-reservas', compact('reservas', 'estadisticas'));
    }
}