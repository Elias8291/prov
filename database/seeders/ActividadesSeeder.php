<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ActividadesSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = public_path('assets/json/actividades.json');
        $jsonData = File::get($jsonPath);
        $data = json_decode($jsonData, true);
        $actividades = [];
        
        foreach ($data['Hoja1'] as $item) {
            $actividades[] = [
                'nombre' => $item['actividad'],
                'sector_id' => $item['id_sector'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        DB::table('actividad')->insert($actividades);
    }
}