<?php

namespace App\Http\Controllers;

use App\Models\DiasInhabiles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiasInhabilesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fecha_inicio' => [
                'required',
                'date',
                // Optional: Prevent overlapping periods
                Rule::unique('dias_inhabiles')->where(function ($query) use ($request) {
                    return $query->where(function ($q) use ($request) {
                        $endDate = $request->fecha_fin ?? $request->fecha_inicio;
                        $q->whereBetween('fecha_inicio', [$request->fecha_inicio, $endDate])
                          ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $endDate])
                          ->orWhereRaw('? BETWEEN fecha_inicio AND COALESCE(fecha_fin, fecha_inicio)', [$request->fecha_inicio])
                          ->orWhereRaw('? BETWEEN fecha_inicio AND COALESCE(fecha_fin, fecha_inicio)', [$endDate]);
                    });
                }),
            ],
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'descripcion' => 'required|string|max:255',
        ]);

        DiasInhabiles::create([
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('citas.index')->with('success', 'Período inhábil agregado exitosamente.');
    }

    public function destroy(DiasInhabiles $diaInhabil)
    {
        $diaInhabil->delete();

        return redirect()->route('citas.index')->with('success', 'Período inhábil eliminado exitosamente.');
    }
}