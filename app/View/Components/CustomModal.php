<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CustomModal extends Component
{
    public $modalId;
    public $title;
    public $message;
    public $showModal;

    public function __construct($modalId = 'customModal', $title = '¡Éxito!', $message = 'Operación completada correctamente.', $showModal = false)
    {
        $this->modalId = $modalId;
        $this->title = $title;
        $this->message = $message;
        $this->showModal = $showModal;
    }

    public function render()
    {
        return view('components.custom-modal');
    }
}