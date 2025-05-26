<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SuccessModal extends Component
{
    public $id;
    public $title;
    public $message;

    public function __construct($id, $title = '¡Operación Exitosa!', $message = 'La acción se completó con éxito.')
    {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
    }

    public function render()
    {
        return view('components.success-modal');
    }
}