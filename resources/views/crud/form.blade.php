@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">{{ $editing ? 'UPDATE RECORD' : 'NEW RECORD' }}</p>
    <h1 class="page-title">{{ $title }}</h1>
    <p class="page-description">Enter the details below to {{ $editing ? 'update this record' : 'add it to your school records' }}.</p>
  </div>
  <a href="{{ route($route.'.index') }}" class="btn btn-quiet"><span aria-hidden="true">←</span> Back to list</a>
</div>
<form method="POST" action="{{ $action }}" class="form-panel">
  @csrf
  @if($editing) @method('PUT') @endif
  <div class="form-panel-heading">
    <h2>Information</h2>
    <p>Complete the form and review your details before saving.</p>
  </div>
  <div class="form-grid">
    @foreach($fields as $name => $f)
      @php [$label, $type] = $f; $options = $f[2] ?? []; $val = old($name, $values[$name] ?? ''); @endphp
      <div @class(['form-field', 'form-field-wide' => $type === 'checkboxes'])>
        @if($type === 'checkboxes')
          <p class="form-field-label">{{ $label }}</p>
        @else
          <label for="field-{{ $name }}">{{ $label }}@if($type === 'password' && $editing) <span class="field-hint">(leave blank to keep)</span>@endif</label>
        @endif
        @if($type === 'checkboxes')
          @php $selected = is_array($val) ? $val : ($editing ? array_keys($options) : []); @endphp
          <div class="permission-grid">
            @foreach($options as $key => $optionLabel)
              <label class="permission-option" for="field-{{ $name }}-{{ $key }}">
                <input id="field-{{ $name }}-{{ $key }}" type="checkbox" name="{{ $name }}[]" value="{{ $key }}" @checked(in_array($key, $selected, true))>
                <span>{{ $optionLabel }}</span>
              </label>
            @endforeach
            </div>
            <p class="field-help">The dashboard is always available. Admin account management is limited to full-access administrators.</p>
        @elseif($type === 'select')
          <select id="field-{{ $name }}" name="{{ $name }}" class="input">
            <option value="">Select {{ strtolower($label) }}</option>
            @foreach($options as $k => $v)<option value="{{ $k }}" @selected((string)$val === (string)$k)>{{ $v }}</option>@endforeach
          </select>
        @else
          <input id="field-{{ $name }}" type="{{ $type }}" name="{{ $name }}" class="input" value="{{ $type === 'password' ? '' : $val }}" @if($type === 'number') step="any" @endif>
        @endif
        @error($name)<p class="field-error">{{ $message }}</p>@enderror
        @if($type === 'checkboxes') @error($name.'.*')<p class="field-error">{{ $message }}</p>@enderror @endif
      </div>
    @endforeach
  </div>
  <div class="form-panel-footer">
    <a href="{{ route($route.'.index') }}" class="btn btn-quiet">Cancel</a>
    <button class="btn" type="submit"><span aria-hidden="true">✓</span> {{ $editing ? 'Save changes' : 'Create record' }}</button>
  </div>
</form>
@endsection
