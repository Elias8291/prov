<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Asegúrate de importar el modelo User
use Spatie\Permission\Models\Role; // Importar el modelo Role de Spatie

class UserSeeder extends Seeder
{
   public function run(): void
{
    // Crear el primer usuario
    $user1 = User::create([
        'nombre' => 'Elias Abisai Ramos Jacinto',
        'correo' => 'eliasrj824@gmail.com',
        'password' => Hash::make('Abisai1456'),
        'rfc' => 'EXAMPLE_RFC123',
        'estado' => 'activo',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Asignar el rol de admin al primer usuario
    $user1->assignRole('admin');

    // Crear el segundo usuario
    $user2 = User::create([
        'nombre' => 'Juan Perez Lopez',
        'correo' => 'juanperez@example.com',
        'password' => Hash::make('Juan12345'),
        'rfc' => 'EXAMPLE_RFC456',
        'estado' => 'activo',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Asignar el rol de admin al segundo usuario
    $user2->assignRole('revisor');
}

    

    
}