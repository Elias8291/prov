<?php

namespace Database\Seeders;

use App\Models\DetalleTramite;
use App\Models\Solicitante;
use App\Models\TipoAsentamiento;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PaisSeeder::class,
            EstadosTableSeeder::class,
            MunicipioSeeder::class,
            LocalidadSeeder::class,
            TiposAsentamientoSeeder::class, 
            AsentamientosSeeder::class,
            SectoresSeeder::class,
            ActividadesSeeder::class,
            SolicitanteSeeder::class,
            TramiteSeeder::class,
            DetalleTramiteSeeder::class,
            ProveedorSeeder::class,
        ]);
    }
}