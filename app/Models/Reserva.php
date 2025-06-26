<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'cancha_id',
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'precio_total',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio_total' => 'decimal:2'
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDuracionAttribute(): string
    {
        $inicio = \Carbon\Carbon::parse($this->hora_inicio);
        $fin = \Carbon\Carbon::parse($this->hora_fin);
        $horas = $fin->diffInHours($inicio);
        $minutos = $fin->diffInMinutes($inicio) % 60;
        
        return $horas . 'h ' . $minutos . 'm';
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', '!=', 'cancelada');
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }
}