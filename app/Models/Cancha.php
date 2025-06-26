<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cancha extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'precio_por_hora',
        'capacidad',
        'hora_apertura',
        'hora_cierre',
        'activa',
        'horarios_disponibles'
    ];

    protected $casts = [
        'precio_por_hora' => 'decimal:2',
        'activa' => 'boolean',
        'horarios_disponibles' => 'array'
    ];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(CanchaImagen::class);
    }

    public function estaDisponible($fecha, $hora_inicio, $hora_fin): bool
    {
        return !$this->reservas()
            ->where('fecha', $fecha)
            ->where(function ($query) use ($hora_inicio, $hora_fin) {
                $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                    ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin])
                    ->orWhere(function ($q) use ($hora_inicio, $hora_fin) {
                        $q->where('hora_inicio', '<', $hora_inicio)
                          ->where('hora_fin', '>', $hora_fin);
                    });
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();
    }

    public function getHorariosDisponiblesAttribute($value)
    {
        if (!$value) {
            return $this->generarHorariosDefault();
        }
        return json_decode($value, true);
    }

    public function generarHorariosDefault(): array
    {
        $horarios = [];
        $inicio = (int) substr($this->hora_apertura, 0, 2);
        $fin = (int) substr($this->hora_cierre, 0, 2);
        
        for ($i = $inicio; $i < $fin; $i++) {
            $hora_inicio = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $hora_fin = str_pad($i + 1, 2, '0', STR_PAD_LEFT) . ':00';
            
            $horarios[] = [
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $hora_fin,
                'activo' => true
            ];
        }
        
        return $horarios;
    }

    public function estaHorarioDisponible($fecha, $hora_inicio, $hora_fin): bool
    {
        // Verificar si el horario está en los horarios disponibles de la cancha
        $horariosDisponibles = $this->horarios_disponibles ?? $this->generarHorariosDefault();
        
        $horarioExiste = collect($horariosDisponibles)->contains(function ($horario) use ($hora_inicio, $hora_fin) {
            return $horario['hora_inicio'] === $hora_inicio && 
                   $horario['hora_fin'] === $hora_fin && 
                   $horario['activo'];
        });

        if (!$horarioExiste) {
            return false;
        }

        // Verificar si no está reservado
        return !$this->reservas()
            ->where('fecha', $fecha)
            ->where('hora_inicio', $hora_inicio)
            ->where('hora_fin', $hora_fin)
            ->where('estado', '!=', 'cancelada')
            ->exists();
    }

    public function getHorariosConDisponibilidad($fecha): array
    {
        $horariosDisponibles = $this->horarios_disponibles ?? $this->generarHorariosDefault();
        
        return collect($horariosDisponibles)->map(function ($horario) use ($fecha) {
            if (!$horario['activo']) {
                return array_merge($horario, ['disponible' => false, 'motivo' => 'Horario no habilitado']);
            }

            $disponible = $this->estaHorarioDisponible($fecha, $horario['hora_inicio'], $horario['hora_fin']);
            
            return array_merge($horario, [
                'disponible' => $disponible,
                'motivo' => $disponible ? null : 'Horario ocupado'
            ]);
        })->toArray();
    }
}