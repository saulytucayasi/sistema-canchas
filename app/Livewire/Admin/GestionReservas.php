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
    public $estado = 'pendiente';
    public $observaciones = '';
    public $precio_total = '';
    public $horarios_seleccionados = [];
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
        if ($this->cancha_id && count($this->horarios_seleccionados) > 0) {
            $cancha = Cancha::find($this->cancha_id);

            if (!$cancha) {
                session()->flash('error', 'Cancha no encontrada.');
                return;
            }

            if ($cancha->precio_por_hora <= 0) {
                session()->flash('error', 'La cancha tiene un precio por hora inválido.');
                return;
            }

            $precioTotal = 0;
            
            foreach ($this->horarios_seleccionados as $horario) {
                try {
                    list($horaInicio, $horaFin) = explode('-', $horario);
                    
                    $horaInicioCarbon = Carbon::createFromFormat('H:i', $horaInicio);
                    $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFin);
                    
                    $duracionMinutos = $horaInicioCarbon->diffInMinutes($horaFinCarbon);
                    $duracionHoras = $duracionMinutos / 60;
                    
                    $precioTotal += $cancha->precio_por_hora * $duracionHoras;
                } catch (\Exception $e) {
                    // En caso de error, usar duración de 1 hora como fallback
                    $precioTotal += $cancha->precio_por_hora;
                }
            }
            
            $this->precio_total = round($precioTotal, 2);
        } else {
            $this->precio_total = 0;
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

    public function toggleHorario($horario)
    {
        if (!$horario['disponible']) {
            return;
        }

        $horarioKey = $horario['hora_inicio'] . '-' . $horario['hora_fin'];
        
        if (in_array($horarioKey, $this->horarios_seleccionados)) {
            // Deseleccionar horario
            $this->horarios_seleccionados = array_filter($this->horarios_seleccionados, function($h) use ($horarioKey) {
                return $h !== $horarioKey;
            });
        } else {
            // Seleccionar horario
            $this->horarios_seleccionados[] = $horarioKey;
        }
        
        $this->calcularPrecio();
    }

    public function createReserva()
    {
        $this->calcularPrecio();
        
        // Validar que haya horarios seleccionados
        if (count($this->horarios_seleccionados) === 0) {
            session()->flash('error', 'Debes seleccionar al menos un horario.');
            return;
        }

        $cancha = Cancha::find($this->cancha_id);
        $reservasCreadas = [];
        
        // Verificar disponibilidad de todos los horarios seleccionados
        foreach ($this->horarios_seleccionados as $horario) {
            list($horaInicio, $horaFin) = explode('-', $horario);
            if (!$cancha->estaHorarioDisponible($this->fecha, $horaInicio, $horaFin)) {
                session()->flash('error', 'El horario ' . $horario . ' ya no está disponible.');
                $this->cargarHorariosDisponibles();
                return;
            }
        }

        // Crear una reserva por cada horario seleccionado
        foreach ($this->horarios_seleccionados as $horario) {
            list($horaInicio, $horaFin) = explode('-', $horario);
            
            // Calcular precio individual para este horario
            $horaInicioCarbon = Carbon::createFromFormat('H:i', $horaInicio);
            $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFin);
            $duracionMinutos = $horaInicioCarbon->diffInMinutes($horaFinCarbon);
            $duracionHoras = $duracionMinutos / 60;
            $precioIndividual = round($cancha->precio_por_hora * $duracionHoras, 2);

            $reserva = Reserva::create([
                'cliente_id' => $this->cliente_id,
                'cancha_id' => $this->cancha_id,
                'user_id' => auth()->id(),
                'fecha' => $this->fecha,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'precio_total' => $precioIndividual,
                'estado' => $this->estado,
                'observaciones' => $this->observaciones,
                'estado_voucher' => 'verificado' // Admin no necesita voucher
            ]);
            
            $reservasCreadas[] = $reserva->id;
        }

        $cantidadReservas = count($reservasCreadas);
        session()->flash('message', $cantidadReservas . ' reserva(s) creada(s) exitosamente. Total: S/ ' . number_format($this->precio_total, 2));
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

    public function verificarVoucher($reservaId, $estado, $comentario = null)
    {
        $reserva = Reserva::find($reservaId);
        $reserva->update([
            'estado_voucher' => $estado,
            'comentario_voucher' => $comentario
        ]);
        
        // Si el voucher es verificado, cambiar estado de reserva a confirmada
        if ($estado === 'verificado') {
            $reserva->update(['estado' => 'confirmada']);
            session()->flash('message', 'Voucher verificado y reserva confirmada exitosamente.');
        } else {
            session()->flash('message', 'Voucher ' . $estado . ' exitosamente.');
        }
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
        $this->estado = 'pendiente';
        $this->observaciones = '';
        $this->precio_total = '';
        $this->horarios_seleccionados = [];
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