<?php

namespace App\Livewire\Cliente;

use App\Models\Cancha;
use App\Models\Cliente;
use App\Models\Reserva;
use Livewire\Component;
use Carbon\Carbon;

class CrearReserva extends Component
{
    public $selectedCancha = null;
    public $fecha = '';
    public $hora_inicio = '';
    public $hora_fin = '';
    public $cliente_id = '';
    public $observaciones = '';
    public $precio_total = 0;
    public $horario_seleccionado = '';

    // Datos para mostrar
    public $canchasDisponibles = [];
    public $horariosDisponibles = [];
    public $clientes = [];

    protected $rules = [
        'selectedCancha' => 'required|exists:canchas,id',
        'fecha' => 'required|date|after_or_equal:today',
        'hora_inicio' => 'required',
        'hora_fin' => 'required|after:hora_inicio',
        'cliente_id' => 'required|exists:clientes,id',
        'observaciones' => 'nullable|string|max:500'
    ];

    public function mount()
    {
        $this->canchasDisponibles = Cancha::where('activa', true)->get();
        $this->clientes = Cliente::where('activo', true)->get();
        $this->fecha = Carbon::today()->format('Y-m-d');
    }

    public function updatedSelectedCancha()
    {
        $this->generarHorariosDisponibles();
        $this->resetHorario();
    }

    public function updatedFecha()
    {
        $this->generarHorariosDisponibles();
        $this->resetHorario();
    }

    public function generarHorariosDisponibles()
    {
        if (!$this->selectedCancha || !$this->fecha) {
            $this->horariosDisponibles = [];
            return;
        }

        $cancha = Cancha::find($this->selectedCancha);
        $this->horariosDisponibles = $cancha->getHorariosConDisponibilidad($this->fecha);
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

    public function calcularPrecio()
    {
        if ($this->selectedCancha && $this->hora_inicio && $this->hora_fin) {
            $cancha = Cancha::find($this->selectedCancha);
            
            try {
                // Asegurar que el formato de hora sea correcto
                $horaInicioStr = strlen($this->hora_inicio) == 5 ? $this->hora_inicio : $this->hora_inicio . ':00';
                $horaFinStr = strlen($this->hora_fin) == 5 ? $this->hora_fin : $this->hora_fin . ':00';
                
                $horaInicio = Carbon::createFromFormat('H:i', $horaInicioStr);
                $horaFin = Carbon::createFromFormat('H:i', $horaFinStr);
                
                // Calcular duración en horas (siempre positiva)
                $duracionMinutos = $horaInicio->diffInMinutes($horaFin);
                $duracionHoras = $duracionMinutos / 60;
                
                $this->precio_total = round($cancha->precio_por_hora * $duracionHoras, 2);
            } catch (\Exception $e) {
                // En caso de error, usar duración de 1 hora como fallback
                $this->precio_total = $cancha->precio_por_hora;
            }
        }
    }

    private function resetHorario()
    {
        $this->hora_inicio = '';
        $this->hora_fin = '';
        $this->horario_seleccionado = '';
        $this->precio_total = 0;
    }

    public function crearReserva()
    {
        $this->validate();

        $cancha = Cancha::find($this->selectedCancha);
        
        // Verificar disponibilidad con el nuevo sistema
        if (!$cancha->estaHorarioDisponible($this->fecha, $this->hora_inicio, $this->hora_fin)) {
            session()->flash('error', 'El horario seleccionado ya no está disponible.');
            $this->generarHorariosDisponibles();
            return;
        }

        Reserva::create([
            'cliente_id' => $this->cliente_id,
            'cancha_id' => $this->selectedCancha,
            'user_id' => auth()->id(),
            'fecha' => $this->fecha,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'precio_total' => $this->precio_total,
            'estado' => 'pendiente',
            'observaciones' => $this->observaciones
        ]);

        session()->flash('message', 'Reserva creada exitosamente.');
        
        // Limpiar formulario
        $this->reset(['selectedCancha', 'hora_inicio', 'hora_fin', 'cliente_id', 'observaciones', 'precio_total', 'horario_seleccionado']);
        $this->fecha = Carbon::today()->format('Y-m-d');
        $this->horariosDisponibles = [];
    }

    public function render()
    {
        return view('livewire.cliente.crear-reserva');
    }
}