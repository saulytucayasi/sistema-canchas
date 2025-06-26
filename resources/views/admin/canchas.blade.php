<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Canchas') }}
            </h2>
            <div class="text-sm text-gray-500">
                Administra las canchas deportivas del sistema
            </div>
        </div>
    </x-slot>

    <x-slot name="breadcrumbs">
        @php
        $breadcrumbs = [
            ['title' => 'Admin', 'url' => route('admin.dashboard')],
            ['title' => 'Gestión de Canchas']
        ];
        @endphp
        <x-breadcrumbs :items="$breadcrumbs" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border border-gray-200">
                <livewire:admin.gestion-canchas />
            </div>
        </div>
    </div>
</x-app-layout>