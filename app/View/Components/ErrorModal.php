<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ErrorModal extends Component
{
    public $id;
    public $title;
    public $message;

    public function __construct($id, $title = '¡Error!', $message = 'Ocurrió un error. Por favor, intenta de nuevo.')
    {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
    }

    public function render()
    {
        return view('components.error-modal');
    }
}