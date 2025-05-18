@props(['field'])

<label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
@if ($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'password')
    @if ($field['type'] === 'password')
        <div class="password-input-wrapper">
            <input type="{{ $field['type'] }}" id="{{ $field['id'] }}" name="{{ $field['name'] }}" class="form-control"
                {{ isset($field['required']) ? 'required' : '' }}
                {{ isset($field['maxlength']) ? 'maxlength="' . $field['maxlength'] . '"' : '' }}
                {{ isset($field['readonly']) ? 'readonly disabled' : '' }}>
            <span class="toggle-password"><i class="fas fa-eye"></i></span>
        </div>
    @else
        <input type="{{ $field['type'] }}" id="{{ $field['id'] }}" name="{{ $field['name'] }}" class="form-control"
            {{ isset($field['required']) ? 'required' : '' }}
            {{ isset($field['maxlength']) ? 'maxlength="' . $field['maxlength'] . '"' : '' }}
            {{ isset($field['readonly']) ? 'readonly disabled' : '' }}>
    @endif
    @if (isset($field['helpText']))
        <small class="form-text text-muted">{{ $field['helpText'] }}</small>
    @endif
@elseif ($field['type'] === 'select')
    <select id="{{ $field['id'] }}" name="{{ $field['name'] }}" class="form-control"
        {{ isset($field['required']) ? 'required' : '' }}>
        @foreach ($field['options'] as $value => $label)
            <option value="{{ $value }}"
                {{ isset($field['default']) && $field['default'] === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
@elseif ($field['type'] === 'checkboxes')
    <div class="permissions-container" id="{{ $field['id'] }}_container">
        @foreach ($field['options'] as $option)
            <div class="permission-item">
                <input type="checkbox" id="{{ $field['id'] . '_' . $option['id'] }}" name="{{ $field['name'] }}[]"
                    value="{{ $option['value'] }}">
                <label for="{{ $field['id'] . '_' . $option['id'] }}">{{ $option['label'] }}</label>
            </div>
        @endforeach
    </div>
@endif
