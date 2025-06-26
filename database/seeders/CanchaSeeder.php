<?php

namespace Database\Seeders;

use App\Models\Cancha;
use App\Models\CanchaImagen;
use Illuminate\Database\Seeder;

class CanchaSeeder extends Seeder
{
    public function run(): void
    {
        $canchas = [
            [
                'nombre' => 'Cancha de Fútbol #1',
                'tipo' => 'futbol',
                'descripcion' => 'Cancha de fútbol profesional con césped sintético de última generación. Ideal para partidos de 11 vs 11.',
                'precio_por_hora' => 25000.00,
                'capacidad' => 22,
                'hora_apertura' => '08:00',
                'hora_cierre' => '22:00',
                'activa' => true,
            ],
            [
                'nombre' => 'Cancha de Fútbol #2',
                'tipo' => 'futbol',
                'descripcion' => 'Cancha de fútbol 7 con iluminación LED. Perfecta para partidos nocturnos.',
                'precio_por_hora' => 18000.00,
                'capacidad' => 14,
                'hora_apertura' => '08:00',
                'hora_cierre' => '23:00',
                'activa' => true,
            ],
            [
                'nombre' => 'Cancha de Tenis #1',
                'tipo' => 'tenis',
                'descripcion' => 'Cancha de tenis con superficie de polvo de ladrillo. Incluye red profesional y marcación oficial.',
                'precio_por_hora' => 15000.00,
                'capacidad' => 4,
                'hora_apertura' => '07:00',
                'hora_cierre' => '21:00',
                'activa' => true,
            ],
            [
                'nombre' => 'Cancha de Básquet',
                'tipo' => 'basquet',
                'descripcion' => 'Cancha de básquetbol cubierta con piso de madera. Aros a altura reglamentaria.',
                'precio_por_hora' => 20000.00,
                'capacidad' => 10,
                'hora_apertura' => '09:00',
                'hora_cierre' => '22:00',
                'activa' => true,
            ],
            [
                'nombre' => 'Cancha de Paddle #1',
                'tipo' => 'paddle',
                'descripcion' => 'Cancha de paddle con cristales templados y césped sintético premium.',
                'precio_por_hora' => 22000.00,
                'capacidad' => 4,
                'hora_apertura' => '08:00',
                'hora_cierre' => '22:00',
                'activa' => true,
            ],
            [
                'nombre' => 'Cancha de Vóley',
                'tipo' => 'volley',
                'descripcion' => 'Cancha de vóleibol de playa con arena importada. Red a altura reglamentaria.',
                'precio_por_hora' => 16000.00,
                'capacidad' => 12,
                'hora_apertura' => '09:00',
                'hora_cierre' => '20:00',
                'activa' => true,
            ]
        ];

        foreach ($canchas as $canchaData) {
            $cancha = Cancha::create($canchaData);
            
            // Crear imágenes ficticias para cada cancha
            for ($i = 1; $i <= 3; $i++) {
                CanchaImagen::create([
                    'cancha_id' => $cancha->id,
                    'ruta_imagen' => "canchas/{$cancha->tipo}_imagen_{$i}.jpg",
                    'nombre_original' => "imagen_{$i}.jpg",
                    'orden' => $i,
                    'es_principal' => $i === 1,
                ]);
            }
        }
    }
}