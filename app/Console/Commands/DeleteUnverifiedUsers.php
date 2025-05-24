<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class DeleteUnverifiedUsers extends Command
{
    protected $signature = 'users:delete-unverified';
    protected $description = 'Elimina permanentemente usuarios no verificados después de 5 minutos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('Ejecutando comando para eliminar permanentemente usuarios no verificados');

        try {
            // Buscar usuarios no verificados con más de 5 minutos desde su creación
            $users = User::where(function($query) {
                    $query->where('estado', 'pendiente')
                          ->orWhereNull('fecha_verificacion_correo');
                })
                ->where('created_at', '<', now()->subMinutes(5))
                ->get();

            if ($users->isEmpty()) {
                $this->info('No se encontraron usuarios no verificados para eliminar.');
                Log::info('No se encontraron usuarios no verificados para eliminar.');
                return;
            }

            $count = 0;
            foreach ($users as $user) {
                DB::beginTransaction();
                try {
                    // Registrar información del usuario antes de eliminarlo
                    Log::info('Procesando usuario no verificado', [
                        'user_id' => $user->id,
                        'correo' => $user->correo,
                        'estado' => $user->estado
                    ]);

                    // Eliminar datos relacionados
                    $solicitante = $user->solicitante;
                    if ($solicitante) {
                        // Comprobar que tramites no sea null antes de iterar
                        $tramites = $solicitante->tramites;
                        if ($tramites) {
                            foreach ($tramites as $tramite) {
                                // Comprobar que documentosSolicitante no sea null antes de iterar
                                $documentos = $tramite->documentosSolicitante;
                                if ($documentos) {
                                    foreach ($documentos as $documento) {
                                        try {
                                            // Desencriptar y eliminar archivo físico
                                            $ruta = Crypt::decryptString($documento->ruta_archivo);
                                            if (Storage::disk('public')->exists($ruta)) {
                                                Storage::disk('public')->delete($ruta);
                                            }
                                        } catch (\Exception $e) {
                                            Log::warning('Error al desencriptar o eliminar archivo', [
                                                'documento_id' => $documento->id,
                                                'error' => $e->getMessage()
                                            ]);
                                        }
                                        // Usar forceDelete si el modelo usa SoftDeletes
                                        $documento->forceDelete();
                                    }
                                }

                                // Eliminar detalles del trámite
                                $detalleTramite = $tramite->detalleTramite;
                                if ($detalleTramite) {
                                    // Eliminar dirección asociada
                                    if ($detalleTramite->direccion) {
                                        $detalleTramite->direccion->forceDelete();
                                    }
                                    $detalleTramite->forceDelete();
                                }

                                $tramite->forceDelete();
                            }
                        }
                        $solicitante->forceDelete();
                    }

                    // Eliminar permanentemente el usuario
                    $user->forceDelete();
                    $count++;

                    DB::commit();
                    Log::info('Usuario eliminado permanentemente', ['user_id' => $user->id]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error al eliminar usuario no verificado', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTrace()
                    ]);
                    $this->error("Error al eliminar usuario ID {$user->id}: {$e->getMessage()}");
                }
            }

            $this->info("Se eliminaron permanentemente $count usuarios no verificados.");
            Log::info("Se eliminaron permanentemente $count usuarios no verificados.");
        } catch (\Exception $e) {
            Log::error('Error al ejecutar el comando: ' . $e->getMessage(), [
                'trace' => $e->getTrace()
            ]);
            $this->error('Ocurrió un error al eliminar usuarios no verificados.');
        }
    }
}