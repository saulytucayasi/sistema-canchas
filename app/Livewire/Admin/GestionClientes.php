<?php

namespace App\Livewire\Admin;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class GestionClientes extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedCliente = null;

    // Formulario
    public $nombre = '';
    public $apellido = '';
    public $email = '';
    public $telefono = '';
    public $documento = '';
    public $fecha_nacimiento = '';
    public $direccion = '';
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'apellido' => 'nullable|string|max:255',
        'email' => 'required|email|unique:clientes,email',
        'telefono' => 'nullable|string|max:20',
        'documento' => 'nullable|string|unique:clientes,documento',
        'fecha_nacimiento' => 'nullable|date|before:today',
        'direccion' => 'nullable|string',
        'activo' => 'boolean'
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

    public function openEditModal($clienteId)
    {
        $this->selectedCliente = Cliente::find($clienteId);
        $this->nombre = $this->selectedCliente->nombre;
        $this->apellido = $this->selectedCliente->apellido;
        $this->email = $this->selectedCliente->email;
        $this->telefono = $this->selectedCliente->telefono;
        $this->documento = $this->selectedCliente->documento;
        $this->fecha_nacimiento = $this->selectedCliente->fecha_nacimiento->format('Y-m-d');
        $this->direccion = $this->selectedCliente->direccion;
        $this->activo = $this->selectedCliente->activo;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function createCliente()
    {
        $this->validate();

        Cliente::create([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'documento' => $this->documento,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'direccion' => $this->direccion,
            'activo' => $this->activo,
        ]);

        session()->flash('message', 'Cliente creado exitosamente.');
        $this->closeCreateModal();
    }

    public function updateCliente()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $this->selectedCliente->id,
            'telefono' => 'required|string|max:20',
            'documento' => 'required|string|unique:clientes,documento,' . $this->selectedCliente->id,
            'fecha_nacimiento' => 'required|date|before:today',
            'direccion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $this->selectedCliente->update([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'documento' => $this->documento,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'direccion' => $this->direccion,
            'activo' => $this->activo,
        ]);

        session()->flash('message', 'Cliente actualizado exitosamente.');
        $this->closeEditModal();
    }

    public function deleteCliente($clienteId)
    {
        $cliente = Cliente::find($clienteId);
        
        // Verificar si tiene reservas activas
        if ($cliente->reservas()->where('estado', '!=', 'cancelada')->where('fecha', '>=', today())->exists()) {
            session()->flash('error', 'No se puede eliminar un cliente con reservas activas.');
            return;
        }

        $cliente->delete();
        session()->flash('message', 'Cliente eliminado exitosamente.');
    }

    public function toggleEstado($clienteId)
    {
        $cliente = Cliente::find($clienteId);
        $cliente->update(['activo' => !$cliente->activo]);
        
        $estado = $cliente->activo ? 'activado' : 'desactivado';
        session()->flash('message', "Cliente {$estado} exitosamente.");
    }

    private function resetForm()
    {
        $this->nombre = '';
        $this->apellido = '';
        $this->email = '';
        $this->telefono = '';
        $this->documento = '';
        $this->fecha_nacimiento = '';
        $this->direccion = '';
        $this->activo = true;
        $this->selectedCliente = null;
    }

    public function render()
    {
        $clientes = Cliente::withCount(['reservas' => function($query) {
                $query->where('estado', '!=', 'cancelada');
            }])
            ->where(function($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('apellido', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('documento', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.gestion-clientes', compact('clientes'));
    }
}