<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LocalidadSeeder extends Seeder
{
    public function run()
    {
        // Define the path to the JSON file
        $jsonPath = public_path('assets/json/localidades.json');

        // Check if the file exists
        if (!File::exists($jsonPath)) {
            $this->command->error("JSON file not found at {$jsonPath}");
            return;
        }

        // Get and decode the contents of the JSON file
        $jsonData = File::get($jsonPath);
        $localidades = json_decode($jsonData, true);

        // Check if the JSON has the expected structure
        if (!isset($localidades['Sheet1'])) {
            $this->command->error("Invalid JSON structure in {$jsonPath}");
            return;
        }

        // Insert each localidad into the database
        foreach ($localidades['Sheet1'] as $localidad) {
            DB::table('localidad')->insert([
                'municipio_id' => $localidad['municipality_id'],
                'nombre' => $localidad['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Localidades have been successfully seeded.');
    }
}