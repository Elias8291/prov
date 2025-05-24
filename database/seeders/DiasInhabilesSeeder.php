<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiasInhabiles;
use Carbon\Carbon;

class DiasInhabilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definimos los días festivos para 2025 (puedes ajustar el año según sea necesario)
        $diasFestivos = [
            [
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => null,
                'descripcion' => 'Año Nuevo',
            ],
            [
                'fecha_inicio' => '2025-02-03',
                'fecha_fin' => null,
                'descripcion' => 'Día de la Constitución Mexicana',
            ],
            [
                'fecha_inicio' => '2025-03-17',
                'fecha_fin' => null,
                'descripcion' => 'Natalicio de Benito Juárez',
            ],
            [
                'fecha_inicio' => '2025-05-01',
                'fecha_fin' => null,
                'descripcion' => 'Día del trabajo',
            ],
            [
                'fecha_inicio' => '2025-09-16',
                'fecha_fin' => null,
                'descripcion' => 'Día de la Independencia',
            ],
            [
                'fecha_inicio' => '2025-11-17',
                'fecha_fin' => null,
                'descripcion' => 'Aniversario de la Revolución Mexicana',
            ],
            [
                'fecha_inicio' => '2025-12-25',
                'fecha_fin' => null,
                'descripcion' => 'Navidad',
            ],
        ];

        // Insertamos los registros en la base de datos
        foreach ($diasFestivos as $dia) {
            DiasInhabiles::create($dia);
        }
    }
}