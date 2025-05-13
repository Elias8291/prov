<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Ejecuta los seeds de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        // Lista de permisos para el padrón de proveedores y otras operaciones
        $permisos = [
            // Permisos para gestión de documentos
            'documentos.revisar',
            'documentos.cargar',
            'documentos.editar',
            'documentos.eliminar',

            // Permisos para solicitantes
            'solicitantes.registrar',
            'solicitantes.ver',
            'solicitantes.editar',
            'solicitantes.eliminar',

            // Permisos para el padrón de proveedores
            'padron-proveedores.ver',
            'padron-proveedores.registrar',
            'padron-proveedores.editar',
            'padron-proveedores.eliminar',
            'padron-proveedores.validar',

            
        ];

        // Crear cada permiso en la base de datos
        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
        }
    }
}