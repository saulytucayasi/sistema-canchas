<?php

namespace App\Livewire\Cliente;

use App\Models\Cancha;
use App\Models\Cliente;
use App\Models\Reserva;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class CrearReserva extends Component
{
    use WithFileUploads;
    public $selectedCancha = null;
    public $fecha = '';
    public $cliente_id = '';
    public $observaciones = '';
    public $precio_total = 0;
    public $voucher_pago;
    public $horarios_seleccionados = [];

    // Datos para mostrar
    public $canchasDisponibles = [];
    public $horariosDisponibles = [];
    public $clientes = [];
    public $isClienteUser = false;

    protected function rules()
    {
        return [
            'selectedCancha' => 'required|exists:canchas,id',
            'fecha' => 'required|date|after_or_equal:today',
            'horarios_seleccionados' => 'required|array|min:1',
            'cliente_id' => 'required|exists:clientes,id',
            'observaciones' => 'nullable|string|max:500',
            'voucher_pago' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function mount($cancha_id = null)
    {
        $this->canchasDisponibles = Cancha::where('activa', true)->get();
        $this->clientes = Cliente::where('activo', true)->get();
        $this->fecha = Carbon::today()->format('Y-m-d');
        
        // Si el usuario es cliente, autoseleccionar su perfil y ocultar selector
        if (auth()->user() && auth()->user()->hasRole('cliente')) {
            $this->isClienteUser = true;
            $cliente = Cliente::where('email', auth()->user()->email)->first();
            if ($cliente) {
                $this->cliente_id = $cliente->id;
            } else {
                // Si no existe el cliente, crearlo automáticamente
                $user = auth()->user();
                $cliente = Cliente::create([
                    'nombre' => $user->name ?? 'Cliente',
                    'apellido' => '',
                    'email' => $user->email,
                    'telefono' => '',
                    'documento' => '',
                    'fecha_nacimiento' => null,
                    'direccion' => '',
                    'activo' => true
                ]);
                $this->cliente_id = $cliente->id;
            }
        }
        
        // Si se pasa un ID de cancha, preseleccionarla
        if ($cancha_id) {
            $this->selectedCancha = $cancha_id;
            $this->generarHorariosDisponibles();
        }
    }

    public function updatedSelectedCancha()
    {
        $this->generarHorariosDisponibles();
        $this->resetHorarios();
    }

    public function updatedFecha()
    {
        $this->generarHorariosDisponibles();
        $this->resetHorarios();
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

    public function calcularPrecio()
    {
        if ($this->selectedCancha && count($this->horarios_seleccionados) > 0) {
            $cancha = Cancha::find($this->selectedCancha);
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

    private function resetHorarios()
    {
        $this->horarios_seleccionados = [];
        $this->precio_total = 0;
    }

    public function crearReserva()
    {
        try {
            $this->validate();

            $cancha = Cancha::find($this->selectedCancha);
            
            // Verificar disponibilidad de todos los horarios seleccionados
            foreach ($this->horarios_seleccionados as $horario) {
                list($horaInicio, $horaFin) = explode('-', $horario);
                if (!$cancha->estaHorarioDisponible($this->fecha, $horaInicio, $horaFin)) {
                    session()->flash('error', 'El horario ' . $horario . ' ya no está disponible.');
                    $this->generarHorariosDisponibles();
                    return;
                }
            }

            // Guardar voucher de pago (una sola vez para todas las reservas)
            $voucherPath = null;
            if ($this->voucher_pago) {
                $voucherPath = $this->voucher_pago->store('vouchers', 'public');
            }

            // Ordenar horarios para encontrar el rango completo
            $horariosOrdenados = $this->horarios_seleccionados;
            sort($horariosOrdenados);
            
            // Obtener hora de inicio del primer horario y hora de fin del último horario
            $primerHorario = explode('-', $horariosOrdenados[0]);
            $ultimoHorario = explode('-', $horariosOrdenados[count($horariosOrdenados) - 1]);
            
            $horaInicio = $primerHorario[0];
            $horaFin = $ultimoHorario[1];

            // Crear una sola reserva que abarque todos los horarios seleccionados
            $reserva = Reserva::create([
                'cliente_id' => $this->cliente_id,
                'cancha_id' => $this->selectedCancha,
                'user_id' => auth()->id(),
                'fecha' => $this->fecha,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'precio_total' => $this->precio_total,
                'estado' => 'pendiente',
                'observaciones' => $this->observaciones,
                'voucher_pago' => $voucherPath,
                'estado_voucher' => 'pendiente'
            ]);

            session()->flash('message', 'Reserva creada exitosamente para ' . count($this->horarios_seleccionados) . ' horario(s). Total: S/ ' . number_format($this->precio_total, 2));
            
            // Limpiar formulario
            $this->reset(['selectedCancha', 'observaciones', 'voucher_pago', 'horarios_seleccionados']);
            $this->fecha = Carbon::today()->format('Y-m-d');
            $this->precio_total = 0;
            $this->horariosDisponibles = [];
            
            // Mantener cliente_id si es usuario cliente
            if ($this->isClienteUser) {
                $cliente = Cliente::where('email', auth()->user()->email)->first();
                if ($cliente) {
                    $this->cliente_id = $cliente->id;
                }
            } else {
                $this->cliente_id = '';
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la reserva: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.cliente.crear-reserva');
    }
}