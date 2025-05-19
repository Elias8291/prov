<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Seccion4 extends Component
{
    public $action;
    public $method;
    public $datosPrevios;
    public $isRevisor;
    public $seccion;
    public $totalSecciones;
    public $isConfirmationSection;
    public $accionistas;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $action = null,
        $method = 'POST',
        $datosPrevios = [],
        $isRevisor = false,
        $seccion = 4,
        $totalSecciones = 6,
        $isConfirmationSection = false,
        $accionistas = []
    ) {
        $this->action = $action ?? route('inscripcion.procesar_seccion');
        $this->method = $method;
        $this->datosPrevios = $datosPrevios;
        $this->isRevisor = $isRevisor;
        $this->seccion = $seccion;
        $this->totalSecciones = $totalSecciones;
        $this->isConfirmationSection = $isConfirmationSection;
        $this->accionistas = $accionistas;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.seccion4');
    }
}