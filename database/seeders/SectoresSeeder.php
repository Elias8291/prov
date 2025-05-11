<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SectoresSeeder extends Seeder
{
    public function run()
    {
        // Ruta al archivo JSON
        $jsonPath = public_path('assets/json/sectores.json');

        // Verificar si el archivo existe
        if (!File::exists($jsonPath)) {
            $this->command->error('El archivo JSON no se encuentra en la ruta: ' . $jsonPath);
            return;
        }

        // Leer el archivo JSON
        $jsonData = File::get($jsonPath);
        $data = json_decode($jsonData, true);

        // Verificar si el JSON se decodificó correctamente
        if ($data === null || !isset($data['Hoja1'])) {
            $this->command->error('Error al decodificar el JSON o estructura incorrecta.');
            return;
        }

        // Preparar los datos para inserción
        $sectores = [];
        foreach ($data['Hoja1'] as $item) {
            $sectores[] = [
                'nombre' => $item['sector'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar los registros
        if (!empty($sectores)) {
            DB::table('sector')->insert($sectores);
            $this->command->info('Insertados ' . count($sectores) . ' sectores.');
        } else {
            $this->command->warn('No se encontraron sectores para insertar.');
        }
    }
}