<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener la ruta del archivo JSON
        $jsonPath = public_path('assets/json/municipios.json');
        
        // Leer y decodificar el archivo JSON
        $json = File::get($jsonPath);
        $data = json_decode($json, true);
        
        // Verificar si la clave 'Sheet1' existe y obtener los municipios
        if (isset($data['Sheet1'])) {
            $municipios = $data['Sheet1'];
            
            // Insertar cada municipio en la base de datos
            foreach ($municipios as $municipio) {
                DB::table('municipio')->insert([
                    'estado_id' => $municipio['state_id'],  // 'state_id' en el JSON se usa como 'estado_id' en la base de datos
                    'nombre' => $municipio['name'],        // 'name' en el JSON se usa como 'nombre' en la base de datos
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
