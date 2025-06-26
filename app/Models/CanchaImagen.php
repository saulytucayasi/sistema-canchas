<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanchaImagen extends Model
{
    use HasFactory;

    protected $fillable = [
        'cancha_id',
        'ruta_imagen',
        'nombre_original',
        'orden',
        'es_principal'
    ];

    protected $casts = [
        'es_principal' => 'boolean'
    ];

    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class);
    }
}