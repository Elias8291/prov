<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use DateTime;

class ProveedorSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = public_path('assets/json/proveedores.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('The proveedores.json file does not exist!');
            return;
        }

        $proveedores = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('JSON parsing error: ' . json_last_error_msg());
            return;
        }

        if (empty($proveedores)) {
            $this->command->error('No data found in proveedores.json!');
            return;
        }

        $solicitanteIds = DB::table('solicitante')->pluck('id')->toArray();

        foreach ($proveedores as $proveedor) {
            // Skip entries missing required keys
            if (!isset($proveedor['proveedor_id']) || 
                !isset($proveedor['id_solicitante']) || 
                !isset($proveedor['estado']) || 
                !isset($proveedor['fecha_registro']) || 
                !isset($proveedor['fecha_vencimiento'])) {
                $this->command->warn('Skipping invalid entry: Missing required fields');
                continue;
            }

            // Validate solicitante_id
            $solicitanteId = null;
            if (in_array($proveedor['id_solicitante'], $solicitanteIds)) {
                $solicitanteId = $proveedor['id_solicitante'];
            } else {
                $this->command->warn('Invalid id_solicitante in JSON: ' . $proveedor['id_solicitante'] . '. Skipping entry.');
                continue;
            }

            // Debug estado value
            $this->command->info('Processing estado: ' . ($proveedor['estado'] ?? 'N/A'));

            // Normalize and map estado
            $estadoRaw = isset($proveedor['estado']) ? trim(strtolower($proveedor['estado'])) : '';
            $estado = match ($estadoRaw) {
                'activo' => 'Activo',
                'cancelado' => 'Inactivo',
                'pendiente renovacion' => 'Pendiente Renovacion',
                default => 'Inactivo',
            };

            // Validate pv (proveedor_id) length
            if (strlen($proveedor['proveedor_id']) > 10) {
                $this->command->warn('Invalid proveedor_id in JSON: ' . $proveedor['proveedor_id'] . ' exceeds 10 characters. Skipping entry.');
                continue;
            }

            // Validate fecha_registro
            $fechaRegistro = $proveedor['fecha_registro'];
            if ($fechaRegistro && !DateTime::createFromFormat('Y-m-d', $fechaRegistro)) {
                $this->command->warn('Invalid fecha_registro in JSON: ' . $proveedor['fecha_registro'] . '. Setting fecha_registro to null.');
                $fechaRegistro = null;
            }

            // Validate fecha_vencimiento
            $fechaVencimiento = $proveedor['fecha_vencimiento'];
            if ($fechaVencimiento && !DateTime::createFromFormat('Y-m-d', $fechaVencimiento)) {
                $this->command->warn('Invalid fecha_vencimiento in JSON: ' . $proveedor['fecha_vencimiento'] . '. Setting fecha_vencimiento to null.');
                $fechaVencimiento = null;
            }

            DB::table('proveedor')->insert([
                'pv' => $proveedor['proveedor_id'],
                'solicitante_id' => $solicitanteId,
                'fecha_registro' => $fechaRegistro,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => $estado,
                'observaciones' => $proveedor['observaciones'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Proveedor records seeded successfully!');
    }
}