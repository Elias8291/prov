<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Seccion1 extends Component
{
    public $action;
    public $method;
    public $tipoPersona;
    public $datosPrevios;
    public $sectores;
    public $isRevisor;
    public $mostrarCurp;
    public $seccion;
    public $totalSecciones;
    public $isConfirmationSection;
    public $actividadesSeleccionadas;
    

    public function __construct(
        $action = null,
        $method = 'POST',
        $tipoPersona = 'Física',
        $datosPrevios = [],
        $sectores = [],
        $isRevisor = false,
        $mostrarCurp = false,
        $seccion = 1,
        $totalSecciones = 3,
        $isConfirmationSection = false,
        
        $actividadesSeleccionadas = []
    ) {
        $this->action = $action ?? route('inscripcion.procesar');
        $this->method = $method;
        $this->tipoPersona = $tipoPersona;
        $this->datosPrevios = $datosPrevios;
        $this->sectores = $sectores;
        $this->isRevisor = $isRevisor;
        $this->mostrarCurp = $mostrarCurp;
        $this->seccion = $seccion;
        $this->totalSecciones = $totalSecciones;
        $this->isConfirmationSection = $isConfirmationSection;
        $this->actividadesSeleccionadas = $actividadesSeleccionadas;
    }

    public function render()
    {
        return view('components.seccion1');
    }
}