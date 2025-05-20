<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Seccion2 extends Component
{
    public $action;
    public $method;
    public $datosPrevios;
    public $direccion;
    public $estados;
    public $isRevisor;
    public $seccion;
    public $totalSecciones;
    public $isConfirmationSection;

    public function __construct(
        $action = null,
        $method = 'POST',
        $datosPrevios = [],
        $direccion = null,
        $estados = [],
        $isRevisor = false,
        $seccion = 2,
        $totalSecciones = 3,
        $isConfirmationSection = false
    ) {
        $this->action = $action ?? route('inscripcion.procesar_seccion');
        $this->method = $method;
        $this->datosPrevios = $datosPrevios;
        $this->direccion = $direccion;
        $this->estados = $estados;
        $this->isRevisor = $isRevisor;
        $this->seccion = $seccion;
        $this->totalSecciones = $totalSecciones;
        $this->isConfirmationSection = $isConfirmationSection;
    }

    public function render()
    {
        return view('components.seccion2');
    }
}