<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    /**
     * Mostrar listado de documentos
     */
    public function index()
    {
        $documentos = Documento::paginate(10);
        return view('documentos.index', [
            'documentos' => $documentos
        ]);
    }

    /**
     * Mostrar formulario para crear documento
     */
    public function create()
    {
        return view('documentos.create');
    }

    /**
     * Guardar nuevo documento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'max:100'],
            'descripcion' => ['required', 'string'],
            'fecha_expiracion' => ['required', 'date'],
            'es_visible' => ['required', 'boolean'],
            'tipo_persona' => ['required', 'string', 'max:50'],
        ]);

        // Crear documento
        Documento::create($validated);

        return redirect()->route('documentos.index')
            ->with('success', 'Documento creado correctamente');
    }

    /**
     * Mostrar un documento específico
     */
    public function show(Documento $documento)
    {
        return view('documentos.show', compact('documento'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Documento $documento)
    {
        return view('documentos.edit', compact('documento'));
    }

    /**
     * Actualizar documento en la base de datos
     */
    public function update(Request $request, Documento $documento)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'max:100'],
            'descripcion' => ['required', 'string'],
            'fecha_expiracion' => ['required', 'date'],
            'es_visible' => ['required', 'boolean'],
            'tipo_persona' => ['required', 'string', 'max:50'],
        ]);

        $documento->update($validated);

        return redirect()->route('documentos.index')
            ->with('success', 'Documento actualizado correctamente');
    }

    /**
     * Eliminar documento
     */
    public function destroy(Documento $documento)
    {
        $documento->delete();

        return redirect()->route('documentos.index')
            ->with('success', 'Documento eliminado correctamente');
    }
}