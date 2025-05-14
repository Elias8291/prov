<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documents = [
            [
                'nombre' => 'Constancia de Situación Fiscal',
                'tipo' => 'Certificado',
                'descripcion' => 'Original, vigente, emitido por el SAT, no mayor a 3 meses',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Identificación Oficial',
                'tipo' => 'Copia',
                'descripcion' => 'Original vigente (INE, pasaporte o cédula profesional)',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Curriculum Actualizado',
                'tipo' => 'Otro',
                'descripcion' => 'Original, con giro, experiencia, clientes y recursos',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Comprobante de Domicilio',
                'tipo' => 'Comprobante',
                'descripcion' => 'Copia simple, no mayor a 3 meses',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Croquis de Localización y Fotografías',
                'tipo' => 'Otro',
                'descripcion' => 'Original, del domicilio del proveedor',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Carta Poder Simple',
                'tipo' => 'Carta',
                'descripcion' => 'Original, con identificación del aceptante, si aplica',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Acuse de Recibo',
                'tipo' => 'Copia',
                'descripcion' => 'Copia simple, última declaración anual y provisionales',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Acta Constitutiva',
                'tipo' => 'Acta',
                'descripcion' => 'Copia simple, notariada, inscrita en el Registro Público',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Modificaciones al Acta',
                'tipo' => 'Acta',
                'descripcion' => 'Copia simple, si aplica',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Poder Notariado',
                'tipo' => 'Copia',
                'descripcion' => 'Copia simple, para actos de administración',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Acta de Nacimiento',
                'tipo' => 'Acta',
                'descripcion' => 'Original, no mayor a 3 meses',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'CURP',
                'tipo' => 'Copia',
                'descripcion' => 'Copia simple, formato actualizado',
                'fecha_expiracion' => Carbon::create(2025, 8, 14),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('documento')->insert($documents);
    }
}