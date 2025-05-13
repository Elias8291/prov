<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DetalleTramiteSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = public_path('assets/json/detalle_tramite.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('The detalle_tramite.json file does not exist!');
            return;
        }

        $detalles = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('JSON parsing error: ' . json_last_error_msg());
            return;
        }

        if (empty($detalles)) {
            $this->command->error('No data found in detalle_tramite.json!');
            return;
        }

        $tramiteIds = DB::table('tramite')->pluck('id')->toArray();
        $direccionIds = DB::table('direccion')->pluck('id')->toArray();
        $contactoIds = DB::table('contacto_solicitante')->pluck('id')->toArray();
        $representanteLegalIds = DB::table('representante_legal')->pluck('id')->toArray();
        $datoConstitutivoIds = DB::table('datos_constitutivo')->pluck('id')->toArray();

        foreach ($detalles as $detalle) {
            // Skip entries missing required keys
            if (!isset($detalle['tramite_id']) || !isset($detalle['razon_social'])) {
                $this->command->warn('Skipping invalid entry: Missing tramite_id or razon_social');
                continue;
            }

            // Validate tramite_id
            $tramiteId = null;
            if (in_array($detalle['tramite_id'], $tramiteIds)) {
                $tramiteId = $detalle['tramite_id'];
            } else {
                $this->command->warn('Invalid tramite_id in JSON: ' . $detalle['tramite_id'] . '. Skipping entry.');
                continue;
            }

            // Validate direccion_id
            $direccionId = null;
            if (isset($detalle['direccion_id']) && in_array($detalle['direccion_id'], $direccionIds)) {
                $direccionId = $detalle['direccion_id'];
            } elseif (isset($detalle['direccion_id'])) {
                $this->command->warn('Invalid direccion_id in JSON: ' . $detalle['direccion_id'] . '. Setting direccion_id to null.');
            }

            // Validate contacto_id
            $contactoId = null;
            if (isset($detalle['contacto_id']) && in_array($detalle['contacto_id'], $contactoIds)) {
                $contactoId = $detalle['contacto_id'];
            } elseif (isset($detalle['contacto_id'])) {
                $this->command->warn('Invalid contacto_id in JSON: ' . $detalle['contacto_id'] . '. Setting contacto_id to null.');
            }

            // Validate representante_legal_id
            $representanteLegalId = null;
            if (isset($detalle['representante_legal_id']) && in_array($detalle['representante_legal_id'], $representanteLegalIds)) {
                $representanteLegalId = $detalle['representante_legal_id'];
            } elseif (isset($detalle['representante_legal_id'])) {
                $this->command->warn('Invalid representante_legal_id in JSON: ' . $detalle['representante_legal_id'] . '. Setting representante_legal_id to null.');
            }

            // Validate dato_constitutivo_id
            $datoConstitutivoId = null;
            if (isset($detalle['dato_constitutivo_id']) && in_array($detalle['dato_constitutivo_id'], $datoConstitutivoIds)) {
                $datoConstitutivoId = $detalle['dato_constitutivo_id'];
            } elseif (isset($detalle['dato_constitutivo_id'])) {
                $this->command->warn('Invalid dato_constitutivo_id in JSON: ' . $detalle['dato_constitutivo_id'] . '. Setting dato_constitutivo_id to null.');
            }

            DB::table('detalle_tramite')->insert([
                'tramite_id' => $tramiteId,
                'razon_social' => $detalle['razon_social'],
                'email' => $detalle['email'] ?? null,
                'telefono' => $detalle['telefono'] ?? null,
                'direccion_id' => $direccionId,
                'contacto_id' => $contactoId,
                'representante_legal_id' => $representanteLegalId,
                'dato_constitutivo_id' => $datoConstitutivoId,
                'sitio_web' => $detalle['sitio_web'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('DetalleTramite records seeded successfully!');
    }
}