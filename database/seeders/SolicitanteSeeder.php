<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SolicitanteSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = public_path('assets/json/solicitante.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('The solicitantes.json file does not exist!');
            return;
        }

        $solicitantes = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('JSON parsing error: ' . json_last_error_msg());
            return;
        }

        if (empty($solicitantes)) {
            $this->command->error('No data found in solicitantes.json!');
            return;
        }

        $userIds = DB::table('users')->pluck('id')->toArray();

        foreach ($solicitantes as $solicitante) {
            // Skip entries missing required keys
            if (!isset($solicitante['tipo_persona']) || !isset($solicitante['rfc'])) {
                $this->command->warn('Skipping invalid entry: Missing tipo_persona or rfc');
                continue;
            }

            // Validate tipo_persona
            $tipoPersona = in_array($solicitante['tipo_persona'], ['Física', 'Moral'])
                ? $solicitante['tipo_persona']
                : 'Física'; // Default to 'Física' if invalid

            // Assign usuario_id only if valid and exists in users table
            $userId = null;
            if (!empty($userIds) && isset($solicitante['usuario_id']) && in_array($solicitante['usuario_id'], $userIds)) {
                $userId = $solicitante['usuario_id'];
            } elseif (isset($solicitante['usuario_id'])) {
                $this->command->warn('Invalid usuario_id in JSON: ' . $solicitante['usuario_id'] . '. Setting usuario_id to null.');
            }

            DB::table('solicitante')->insert([
                'usuario_id' => $userId,
                'tipo_persona' => $tipoPersona,
                'curp' => $solicitante['curp'] ?? null,
                'rfc' => $solicitante['rfc'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Solicitante records seeded successfully!');
    }
}