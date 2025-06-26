<?php

namespace App\Livewire\Admin;

use App\Models\Cancha;
use App\Models\CanchaImagen;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class GestionCanchas extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedCancha = null;

    // Formulario
    public $nombre = '';
    public $tipo = '';
    public $descripcion = '';
    public $precio_por_hora = '';
    public $capacidad = '';
    public $hora_apertura = '';
    public $hora_cierre = '';
    public $activa = true;
    public $imagenes = [];
    public $horarios_disponibles = [];
    public $mostrarHorarios = false;

    // Tipos disponibles
    public $tiposDisponibles = [
        'futbol' => 'Fútbol',
        'tenis' => 'Tenis',
        'basquet' => 'Básquet',
        'paddle' => 'Paddle',
        'volley' => 'Vóley'
    ];

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|in:futbol,tenis,basquet,paddle,volley',
        'descripcion' => 'nullable|string',
        'precio_por_hora' => 'required|numeric|min:0',
        'capacidad' => 'required|integer|min:1',
        'hora_apertura' => 'required',
        'hora_cierre' => 'required|after:hora_apertura',
        'activa' => 'boolean',
        'imagenes.*' => 'nullable|image|max:2048'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
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

    public function openEditModal($canchaId)
    {
        $this->selectedCancha = Cancha::find($canchaId);
        $this->nombre = $this->selectedCancha->nombre;
        $this->tipo = $this->selectedCancha->tipo;
        $this->descripcion = $this->selectedCancha->descripcion;
        $this->precio_por_hora = $this->selectedCancha->precio_por_hora;
        $this->capacidad = $this->selectedCancha->capacidad;
        $this->hora_apertura = $this->selectedCancha->hora_apertura;
        $this->hora_cierre = $this->selectedCancha->hora_cierre;
        $this->activa = $this->selectedCancha->activa;
        $this->horarios_disponibles = $this->selectedCancha->horarios_disponibles ?? [];
        $this->mostrarHorarios = !empty($this->horarios_disponibles);
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function createCancha()
    {
        $this->validate();

        $cancha = Cancha::create([
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'descripcion' => $this->descripcion,
            'precio_por_hora' => $this->precio_por_hora,
            'capacidad' => $this->capacidad,
            'hora_apertura' => $this->hora_apertura,
            'hora_cierre' => $this->hora_cierre,
            'activa' => $this->activa,
            'horarios_disponibles' => $this->mostrarHorarios ? $this->horarios_disponibles : null,
        ]);

        // Procesar imágenes
        $this->procesarImagenes($cancha);

        session()->flash('message', 'Cancha creada exitosamente.');
        $this->closeCreateModal();
    }

    public function updateCancha()
    {
        $this->validate();

        $this->selectedCancha->update([
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'descripcion' => $this->descripcion,
            'precio_por_hora' => $this->precio_por_hora,
            'capacidad' => $this->capacidad,
            'hora_apertura' => $this->hora_apertura,
            'hora_cierre' => $this->hora_cierre,
            'activa' => $this->activa,
            'horarios_disponibles' => $this->mostrarHorarios ? $this->horarios_disponibles : null,
        ]);

        // Procesar nuevas imágenes
        $this->procesarImagenes($this->selectedCancha);

        session()->flash('message', 'Cancha actualizada exitosamente.');
        $this->closeEditModal();
    }

    public function deleteCancha($canchaId)
    {
        $cancha = Cancha::find($canchaId);
        
        // Verificar si tiene reservas activas
        if ($cancha->reservas()->where('estado', '!=', 'cancelada')->where('fecha', '>=', today())->exists()) {
            session()->flash('error', 'No se puede eliminar una cancha con reservas activas.');
            return;
        }

        // Eliminar imágenes del storage
        foreach ($cancha->imagenes as $imagen) {
            if (file_exists(storage_path('app/public/' . $imagen->ruta_imagen))) {
                unlink(storage_path('app/public/' . $imagen->ruta_imagen));
            }
        }

        $cancha->delete();
        session()->flash('message', 'Cancha eliminada exitosamente.');
    }

    public function toggleEstado($canchaId)
    {
        $cancha = Cancha::find($canchaId);
        $cancha->update(['activa' => !$cancha->activa]);
        
        $estado = $cancha->activa ? 'activada' : 'desactivada';
        session()->flash('message', "Cancha {$estado} exitosamente.");
    }

    public function eliminarImagen($imagenId)
    {
        $imagen = CanchaImagen::find($imagenId);
        
        if (file_exists(storage_path('app/public/' . $imagen->ruta_imagen))) {
            unlink(storage_path('app/public/' . $imagen->ruta_imagen));
        }
        
        $imagen->delete();
        session()->flash('message', 'Imagen eliminada exitosamente.');
    }

    private function procesarImagenes($cancha)
    {
        if (!empty($this->imagenes)) {
            $orden = $cancha->imagenes()->max('orden') + 1;
            
            foreach ($this->imagenes as $imagen) {
                $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
                $rutaImagen = $imagen->storeAs('canchas', $nombreArchivo, 'public');
                
                CanchaImagen::create([
                    'cancha_id' => $cancha->id,
                    'ruta_imagen' => $rutaImagen,
                    'nombre_original' => $imagen->getClientOriginalName(),
                    'orden' => $orden,
                    'es_principal' => $cancha->imagenes()->count() === 0
                ]);
                
                $orden++;
            }
        }
    }

    public function generarHorarios()
    {
        if (!$this->hora_apertura || !$this->hora_cierre) {
            session()->flash('error', 'Debe especificar hora de apertura y cierre primero.');
            return;
        }

        $inicio = (int) substr($this->hora_apertura, 0, 2);
        $fin = (int) substr($this->hora_cierre, 0, 2);
        
        if ($inicio >= $fin) {
            session()->flash('error', 'La hora de apertura debe ser menor que la hora de cierre.');
            return;
        }

        $this->horarios_disponibles = [];
        for ($i = $inicio; $i < $fin; $i++) {
            $hora_inicio = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $hora_fin = str_pad($i + 1, 2, '0', STR_PAD_LEFT) . ':00';
            
            $this->horarios_disponibles[] = [
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $hora_fin,
                'activo' => true
            ];
        }
        
        $this->mostrarHorarios = true;
    }

    public function toggleHorario($index)
    {
        if (isset($this->horarios_disponibles[$index])) {
            $this->horarios_disponibles[$index]['activo'] = !$this->horarios_disponibles[$index]['activo'];
        }
    }

    public function agregarHorario()
    {
        $this->horarios_disponibles[] = [
            'hora_inicio' => '',
            'hora_fin' => '',
            'activo' => true
        ];
    }

    public function eliminarHorario($index)
    {
        if (isset($this->horarios_disponibles[$index])) {
            unset($this->horarios_disponibles[$index]);
            $this->horarios_disponibles = array_values($this->horarios_disponibles);
        }
    }

    private function resetForm()
    {
        $this->nombre = '';
        $this->tipo = '';
        $this->descripcion = '';
        $this->precio_por_hora = '';
        $this->capacidad = '';
        $this->hora_apertura = '';
        $this->hora_cierre = '';
        $this->activa = true;
        $this->imagenes = [];
        $this->horarios_disponibles = [];
        $this->mostrarHorarios = false;
        $this->selectedCancha = null;
    }

    public function render()
    {
        $canchas = Cancha::with(['imagenes' => function($query) {
                $query->where('es_principal', true);
            }])
            ->where(function($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('tipo', 'like', '%' . $this->search . '%');
            })
            ->paginate(8);

        return view('livewire.admin.gestion-canchas', compact('canchas'));
    }
}