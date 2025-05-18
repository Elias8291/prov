@props([
    'modalId' => 'modal',
    'title' => 'Modal',
    'type' => 'form', // 'form' for add/edit, 'confirm' for delete
    'formAction' => '',
    'method' => 'POST',
    'fields' => [],
    'submitText' => 'Guardar',
    'closeBtnId' => 'closeModal',
    'message' => null,
    'warning' => null,
    'warningId' => null,
    'warningMessage' => null
])

<div id="{{ $modalId }}" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">{{ $title }}</h2>
            <span class="close-modal">×</span>
        </div>

        <form id="{{ $modalId }}Form" action="{{ $formAction }}" method="POST">
            @csrf
            @if ($type === 'form' && $method === 'PUT')
                @method('PUT')
            @elseif ($type === 'confirm')
                @method('DELETE')
            @endif

            <div class="modal-body">
                @if ($type === 'form')
                    @foreach ($fields as $field)
                        <div class="{{ isset($field['row']) ? 'form-row' : 'form-group' }}">
                            @if (isset($field['row']))
                                @foreach ($field['row'] as $child)
                                    <div class="form-group {{ $child['class'] ?? 'col-md-6' }}">
                                        @include('components.modal-field', ['field' => $child])
                                    </div>
                                @endforeach
                            @else
                                @include('components.modal-field', ['field' => $field])
                            @endif
                        </div>
                    @endforeach
                @else
                    @if ($message)
                        <p class="delete-message">{{ $message }}</p>
                    @endif
                    @if ($warning)
                        <p class="delete-warning">{{ $warning }}</p>
                    @endif
                    <div class="user-info">
                        @foreach ($fields as $field)
                            <p><strong>{{ $field['label'] }}:</strong> <span id="{{ $field['id'] }}"></span></p>
                        @endforeach
                    </div>
                    @if ($warningId && $warningMessage)
                        <div id="{{ $warningId }}" class="alert alert-warning" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i> {{ $warningMessage }}
                        </div>
                    @endif
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="{{ $closeBtnId }}">Cancelar</button>
                <button type="submit" class="{{ $type === 'confirm' ? 'btn-danger' : 'btn-primary' }}" id="submitBtn">
                    <i class="fas {{ $type === 'confirm' ? 'fa-trash' : 'fa-save' }}"></i> {{ $submitText }}
                </button>
            </div>
        </form>
    </div>
</div>