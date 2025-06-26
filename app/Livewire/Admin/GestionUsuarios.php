<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class GestionUsuarios extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedUser = null;

    // Formulario
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRole = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'selectedRole' => 'required|exists:roles,name'
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

    public function openEditModal($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->name = $this->selectedUser->name;
        $this->email = $this->selectedUser->email;
        $this->selectedRole = $this->selectedUser->roles->first()?->name ?? '';
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function createUser()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($this->selectedRole);

        session()->flash('message', 'Usuario creado exitosamente.');
        $this->closeCreateModal();
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->selectedUser->id,
            'selectedRole' => 'required|exists:roles,name'
        ]);

        $this->selectedUser->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($this->password) {
            $this->selectedUser->update([
                'password' => Hash::make($this->password)
            ]);
        }

        $this->selectedUser->syncRoles([$this->selectedRole]);

        session()->flash('message', 'Usuario actualizado exitosamente.');
        $this->closeEditModal();
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);
        
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario.');
            return;
        }

        $user->delete();
        session()->flash('message', 'Usuario eliminado exitosamente.');
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRole = '';
        $this->selectedUser = null;
    }

    public function render()
    {
        $users = User::with('roles')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.admin.gestion-usuarios', compact('users', 'roles'));
    }
}