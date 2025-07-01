<?php

namespace App\Livewire\Secretaria;

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

    public function toggleHorario($horario)
    {
        if (!$horario['disponible']) {
            return;
        }

        $horarioKey = $horario['hora_inicio'] . '-' . $horario['hora_fin'];
        
        if (in_array($horarioKey, $this->horarios_seleccionados)) {
            $this->horarios_seleccionados = array_filter($this->horarios_seleccionados, function($h) use ($horarioKey) {
                return $h !== $horarioKey;
            });
        } else {
            $this->horarios_seleccionados[] = $horarioKey;
        }
        
        $this->calcularPrecio();
    }

    public function calcularPrecio()
    {
        if ($this->cancha_id && count($this->horarios_seleccionados) > 0) {
            $cancha = Cancha::find($this->cancha_id);
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

    public function createReserva()
    {
        if (count($this->horarios_seleccionados) === 0) {
            session()->flash('error', 'Debes seleccionar al menos un horario.');
            return;
        }

        $cancha = Cancha::find($this->cancha_id);
        $reservasCreadas = [];
        
        foreach ($this->horarios_seleccionados as $horario) {
            list($horaInicio, $horaFin) = explode('-', $horario);
            if (!$cancha->estaHorarioDisponible($this->fecha, $horaInicio, $horaFin)) {
                session()->flash('error', 'El horario ' . $horario . ' ya no está disponible.');
                return;
            }
        }

        foreach ($this->horarios_seleccionados as $horario) {
            list($horaInicio, $horaFin) = explode('-', $horario);
            
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
                'estado_voucher' => 'verificado' // Secretario no necesita voucher
            ]);
            
            $reservasCreadas[] = $reserva->id;
        }

        $cantidadReservas = count($reservasCreadas);
        session()->flash('message', $cantidadReservas . ' reserva(s) creada(s) exitosamente.');
        $this->closeCreateModal();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
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

    public function verificarVoucher($reservaId, $estado, $comentario = null)
    {
        $reserva = Reserva::find($reservaId);
        $reserva->update([
            'estado_voucher' => $estado,
            'comentario_voucher' => $comentario
        ]);
        
        if ($estado === 'verificado') {
            $reserva->update(['estado' => 'confirmada']);
            session()->flash('message', 'Voucher verificado y reserva confirmada exitosamente.');
        } else {
            session()->flash('message', 'Voucher ' . $estado . ' exitosamente.');
        }
    }

    public function render()
    {
        return view('livewire.secretaria.gestion-reservas');
    }
}
