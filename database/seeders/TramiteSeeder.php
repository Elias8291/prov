<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TramiteSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = public_path('assets/json/tramites.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('The tramites.json file does not exist!');
            return;
        }

        $tramites = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('JSON parsing error: ' . json_last_error_msg());
            return;
        }

        if (empty($tramites)) {
            $this->command->error('No data found in tramites.json!');
            return;
        }

        $solicitanteIds = DB::table('solicitante')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        foreach ($tramites as $tramite) {
            // Skip entries missing required keys
            if (!isset($tramite['tipo_tramite']) || !isset($tramite['estado'])) {
                $this->command->warn('Skipping invalid entry: Missing tipo_tramite or estado');
                continue;
            }

            // Validate tipo_tramite
            $tipoTramite = in_array($tramite['tipo_tramite'], ['Inscripcion', 'Renovacion', 'Actualizacion'])
                ? $tramite['tipo_tramite']
                : 'Inscripcion'; // Default to 'Inscripcion' if invalid

            // Validate estado
            $estado = in_array($tramite['estado'], ['Pendiente', 'En Revision', 'Aprobado', 'Rechazado'])
                ? $tramite['estado']
                : 'Pendiente'; // Default to 'Pendiente' if invalid

            // Validate solicitante_id
            $solicitanteId = null;
            if (isset($tramite['solicitante_id']) && in_array($tramite['solicitante_id'], $solicitanteIds)) {
                $solicitanteId = $tramite['solicitante_id'];
            } elseif (isset($tramite['solicitante_id'])) {
                $this->command->warn('Invalid solicitante_id in JSON: ' . $tramite['solicitante_id'] . '. Setting solicitante_id to null.');
            }

            // Validate revisado_por
            $revisadoPor = null;
            if (isset($tramite['revisado_por']) && in_array($tramite['revisado_por'], $userIds)) {
                $revisadoPor = $tramite['revisado_por'];
            } elseif (isset($tramite['revisado_por'])) {
                $this->command->warn('Invalid revisado_por in JSON: ' . $tramite['revisado_por'] . '. Setting revisado_por to null.');
            }

            // Validate progreso_tramite
            $progresoTramite = isset($tramite['progreso_tramite']) && is_numeric($tramite['progreso_tramite'])
                ? max(0, min(100, (int)$tramite['progreso_tramite'])) // Ensure between 0 and 100
                : 0;

            DB::table('tramite')->insert([
                'solicitante_id' => $solicitanteId,
                'tipo_tramite' => $tipoTramite,
                'estado' => $estado,
                'progreso_tramite' => $progresoTramite,
                'revisado_por' => $revisadoPor,
                'fecha_revision' => $tramite['fecha_revision'] ?? null,
                'fecha_inicio' => $tramite['fecha_inicio'] ?? null,
                'fecha_finalizacion' => $tramite['fecha_finalizacion'] ?? null,
                'observaciones' => $tramite['observaciones'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Tramite records seeded successfully!');
    }
}