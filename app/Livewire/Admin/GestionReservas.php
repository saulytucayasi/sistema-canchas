<?php

namespace App\Livewire\Admin;

use App\Models\Reserva;
use App\Models\Cancha;
use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class GestionReservas extends Component
{
    use WithPagination;

    public $search = '';
    public $filtroEstado = '';
    public $filtroFecha = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedReserva = null;

    // Formulario
    public $cliente_id = '';
    public $cancha_id = '';
    public $fecha = '';
    public $hora_inicio = '';
    public $hora_fin = '';
    public $estado = 'pendiente';
    public $observaciones = '';
    public $precio_total = '';
    public $horario_seleccionado = '';
    public $horariosDisponibles = [];

    // Estados disponibles
    public $estadosDisponibles = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'cancelada' => 'Cancelada',
        'completada' => 'Completada'
    ];

    protected $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'cancha_id' => 'required|exists:canchas,id',
        'fecha' => 'required|date|after_or_equal:today',
        'hora_inicio' => 'required',
        'hora_fin' => 'required',
        'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
        'observaciones' => 'nullable|string|max:500',
        'precio_total' => 'required|numeric|min:0'
    ];

    public function mount()
    {
        $this->filtroFecha = Carbon::today()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatingFiltroFecha()
    {
        $this->resetPage();
    }

    public function updatedCanchaId()
    {
        $this->cargarHorariosDisponibles();
        $this->horario_seleccionado = '';
        $this->hora_inicio = '';
        $this->hora_fin = '';
        $this->precio_total = '';
    }

    public function updatedFecha()
    {
        $this->cargarHorariosDisponibles();
        $this->horario_seleccionado = '';
        $this->hora_inicio = '';
        $this->hora_fin = '';
        $this->precio_total = '';
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->fecha = Carbon::today()->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($reservaId)
    {
        $this->selectedReserva = Reserva::with(['cliente', 'cancha'])->find($reservaId);
        $this->cliente_id = $this->selectedReserva->cliente_id;
        $this->cancha_id = $this->selectedReserva->cancha_id;
        $this->fecha = $this->selectedReserva->fecha->format('Y-m-d');
        $this->hora_inicio = $this->selectedReserva->hora_inicio;
        $this->hora_fin = $this->selectedReserva->hora_fin;
        $this->estado = $this->selectedReserva->estado;
        $this->observaciones = $this->selectedReserva->observaciones;
        $this->precio_total = $this->selectedReserva->precio_total;
        
        // Cargar horarios disponibles y establecer el horario seleccionado
        $this->cargarHorariosDisponibles();
        $this->horario_seleccionado = $this->hora_inicio . '-' . $this->hora_fin;
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function calcularPrecio()
    {
        if ($this->cancha_id && $this->hora_inicio && $this->hora_fin) {
            $cancha = Cancha::find($this->cancha_id);

            if (!$cancha) {
                session()->flash('error', 'Cancha no encontrada.');
                return;
            }

            if ($cancha->precio_por_hora <= 0) {
                session()->flash('error', 'La cancha tiene un precio por hora inválido.');
                return;
            }

            try {
                // Asegurar que el formato de hora sea correcto
                $horaInicioStr = strlen($this->hora_inicio) == 5 ? $this->hora_inicio : $this->hora_inicio . ':00';
                $horaFinStr = strlen($this->hora_fin) == 5 ? $this->hora_fin : $this->hora_fin . ':00';
                
                $horaInicio = Carbon::createFromFormat('H:i', $horaInicioStr);
                $horaFin = Carbon::createFromFormat('H:i', $horaFinStr);

                if ($horaFin->lte($horaInicio)) {
                    session()->flash('error', 'La hora de fin debe ser mayor que la de inicio.');
                    return;
                }

                // Calcular duración en horas (siempre positiva)
                $duracionMinutos = $horaInicio->diffInMinutes($horaFin);
                $duracionHoras = $duracionMinutos / 60;
                
                $this->precio_total = round($cancha->precio_por_hora * $duracionHoras, 2);
            } catch (\Exception $e) {
                session()->flash('error', 'Error al calcular el precio: ' . $e->getMessage());
                // En caso de error, usar duración de 1 hora como fallback
                $this->precio_total = $cancha->precio_por_hora;
            }
        }
    }


    public function cargarHorariosDisponibles()
    {
        if ($this->cancha_id && $this->fecha) {
            $cancha = Cancha::find($this->cancha_id);
            $this->horariosDisponibles = $cancha->getHorariosConDisponibilidad($this->fecha);
        } else {
            $this->horariosDisponibles = [];
        }
    }

    public function seleccionarHorario($horario)
    {
        if ($horario['disponible']) {
            $this->hora_inicio = $horario['hora_inicio'];
            $this->hora_fin = $horario['hora_fin'];
            $this->horario_seleccionado = $horario['hora_inicio'] . '-' . $horario['hora_fin'];
            $this->calcularPrecio();
        }
    }

    public function createReserva()
    {
        $this->calcularPrecio();
        $this->validate();

        $cancha = Cancha::find($this->cancha_id);
        
        // Verificar disponibilidad con el nuevo sistema de horarios
        if (!$cancha->estaHorarioDisponible($this->fecha, $this->hora_inicio, $this->hora_fin)) {
            session()->flash('error', 'El horario seleccionado no está disponible.');
            return;
        }

        Reserva::create([
            'cliente_id' => $this->cliente_id,
            'cancha_id' => $this->cancha_id,
            'user_id' => auth()->id(),
            'fecha' => $this->fecha,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'precio_total' => $this->precio_total,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones
        ]);

        session()->flash('message', 'Reserva creada exitosamente.');
        $this->closeCreateModal();
    }

    public function updateReserva()
    {
        $this->calcularPrecio();
        $this->validate();

        $cancha = Cancha::find($this->cancha_id);
        
        // Verificar disponibilidad (excluyendo la reserva actual)
        $reservasConflicto = $cancha->reservas()
            ->where('id', '!=', $this->selectedReserva->id)
            ->where('fecha', $this->fecha)
            ->where(function ($query) {
                $query->whereBetween('hora_inicio', [$this->hora_inicio, $this->hora_fin])
                    ->orWhereBetween('hora_fin', [$this->hora_inicio, $this->hora_fin])
                    ->orWhere(function ($q) {
                        $q->where('hora_inicio', '<', $this->hora_inicio)
                          ->where('hora_fin', '>', $this->hora_fin);
                    });
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($reservasConflicto) {
            session()->flash('error', 'La cancha no está disponible en ese horario.');
            return;
        }

        $this->selectedReserva->update([
            'cliente_id' => $this->cliente_id,
            'cancha_id' => $this->cancha_id,
            'fecha' => $this->fecha,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'precio_total' => $this->precio_total,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones
        ]);

        session()->flash('message', 'Reserva actualizada exitosamente.');
        $this->closeEditModal();
    }

    public function cambiarEstado($reservaId, $nuevoEstado)
    {
        $reserva = Reserva::find($reservaId);
        $reserva->update(['estado' => $nuevoEstado]);
        
        session()->flash('message', "Reserva {$nuevoEstado} exitosamente.");
    }

    public function deleteReserva($reservaId)
    {
        $reserva = Reserva::find($reservaId);
        $reserva->delete();
        session()->flash('message', 'Reserva eliminada exitosamente.');
    }

    private function resetForm()
    {
        $this->cliente_id = '';
        $this->cancha_id = '';
        $this->fecha = '';
        $this->hora_inicio = '';
        $this->hora_fin = '';
        $this->estado = 'pendiente';
        $this->observaciones = '';
        $this->precio_total = '';
        $this->horario_seleccionado = '';
        $this->horariosDisponibles = [];
        $this->selectedReserva = null;
    }

    public function render()
    {
        $query = Reserva::with(['cliente', 'cancha', 'usuario']);

        // Filtro por búsqueda
        if ($this->search) {
            $query->whereHas('cliente', function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('apellido', 'like', '%' . $this->search . '%');
            })->orWhereHas('cancha', function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por estado
        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        // Filtro por fecha
        if ($this->filtroFecha) {
            $query->whereDate('fecha', $this->filtroFecha);
        }

        $reservas = $query->orderBy('fecha', 'desc')
                         ->orderBy('hora_inicio', 'desc')
                         ->paginate(10);

        $clientes = Cliente::where('activo', true)->get();
        $canchas = Cancha::where('activa', true)->get();

        return view('livewire.admin.gestion-reservas', compact('reservas', 'clientes', 'canchas'));
    }
}