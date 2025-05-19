<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Seccion3 extends Component
{
    public $action;
    public $method;
    public $datosPrevios;
    public $estados;
    public $isRevisor;
    public $seccion;
    public $totalSecciones;
    public $isConfirmationSection;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $action = null,
        $method = 'POST',
        $datosPrevios = [],
        $estados = [],
        $isRevisor = false,
        $seccion = 3,
        $totalSecciones = 6,
        $isConfirmationSection = false
    ) {
        $this->action = $action ?? route('inscripcion.procesar_seccion');
        $this->method = $method;
        $this->datosPrevios = $datosPrevios;
        $this->estados = $estados;
        $this->isRevisor = $isRevisor;
        $this->seccion = $seccion;
        $this->totalSecciones = $totalSecciones;
        $this->isConfirmationSection = $isConfirmationSection;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.seccion3');
    }
}