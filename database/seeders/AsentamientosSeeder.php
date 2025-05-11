<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AsentamientosSeeder extends Seeder
{
    /**
     * Tamaño del bloque para inserción por lotes
     * @var int
     */
    protected $chunkSize = 100;

    public function run()
    {
        // Ruta al archivo JSON
        $jsonPath = public_path('assets/json/asentamientos.json');

        // Verificar si el archivo existe
        if (!File::exists($jsonPath)) {
            $this->command->error('El archivo JSON no se encuentra en la ruta: ' . $jsonPath);
            return;
        }

        // Leer el archivo JSON
        $jsonData = File::get($jsonPath);
        $data = json_decode($jsonData, true);

        // Verificar si el JSON se decodificó correctamente
        if ($data === null || !isset($data['settlements'])) {
            $this->command->error('Error al decodificar el JSON o estructura incorrecta.');
            return;
        }

        // Preparar los datos para inserción por bloques
        $asentamientos = [];
        foreach ($data['settlements'] as $settlement) {
            $asentamientos[] = [
                'nombre' => $settlement['name'],
                'codigo_postal' => $settlement['zip_code'],
                'localidad_id' => $settlement['localidad_id'],
                'tipo_asentamiento_id' => $settlement['settlement_type_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insertar cuando alcancemos el tamaño del bloque
            if (count($asentamientos) >= $this->chunkSize) {
                DB::table('asentamiento')->insert($asentamientos);
                $asentamientos = []; // Resetear el array
                $this->command->info('Insertados ' . $this->chunkSize . ' registros...');
            }
        }

        // Insertar los registros restantes (si los hay)
        if (!empty($asentamientos)) {
            DB::table('asentamiento')->insert($asentamientos);
            $this->command->info('Insertados ' . count($asentamientos) . ' registros finales...');
        }

        $this->command->info('Total de asentamientos insertados: ' . count($data['settlements']));
    }
}