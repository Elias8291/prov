<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Solicitante;
use App\Models\Tramite;
use App\Models\DiasInhabiles;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Cita::with(['solicitante', 'tramite'])->paginate(10);
        $solicitantes = Solicitante::all();
        $tramites = Tramite::all();
        $diasInhabiles = DiasInhabiles::all();

        return view('citas.index', compact('citas', 'solicitantes', 'tramites', 'diasInhabiles'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'solicitante_id' => 'required|exists:solicitante,id',
            'tramite_id' => 'required|exists:tramite,id',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required',
            'estado' => 'required|in:Pendiente,Confirmada,Cancelada,Completada',
            'observaciones' => 'nullable|string',
        ]);

        $cita->update([
            'solicitante_id' => $request->solicitante_id,
            'tramite_id' => $request->tramite_id,
            'fecha_cita' => $request->fecha_cita,
            'hora_cita' => $request->hora_cita,
            'estado' => $request->estado,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada exitosamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')->with('success', 'Cita eliminada exitosamente.');
    }
}