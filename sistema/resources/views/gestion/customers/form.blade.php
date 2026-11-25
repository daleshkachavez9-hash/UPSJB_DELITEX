{{-- Formulario compartido por create.blade.php y edit.blade.php --}}
{{-- En edit existe $customer; en create no, y ?? '' deja el campo vacío --}}
<div class="row g-3">
  @php $tipoDoc = old('document_type', $customer->document_type ?? 'DNI'); @endphp
  <div class="col-md-4">
    <label for="document_number" class="form-label">Documento</label>
    {{-- DNI (8 dígitos) para personas; RUC (11 dígitos) para clubes, colegios y empresas --}}
    <div class="input-group has-validation">
      <select name="document_type" id="document_type" class="form-select flex-grow-0 w-auto @error('document_type') is-invalid @enderror">
        @foreach (\App\Models\Customer::DOCUMENTOS as $tipo => $digitos)
          <option value="{{ $tipo }}" data-digitos="{{ $digitos }}" @selected($tipoDoc === $tipo)>{{ $tipo }}</option>
        @endforeach
      </select>
      <input type="text" name="document_number" id="document_number" inputmode="numeric"
             maxlength="{{ \App\Models\Customer::DOCUMENTOS[$tipoDoc] ?? 8 }}" placeholder="{{ \App\Models\Customer::DOCUMENTOS[$tipoDoc] ?? 8 }} dígitos"
             class="form-control @error('document_number') is-invalid @enderror"
             value="{{ old('document_number', $customer->document_number ?? '') }}">
      @error('document_number')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      @error('document_type')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="col-md-8">
    <label for="name" class="form-label">Nombres y apellidos o razón social</label>
    <input type="text" name="name" id="name" placeholder="Ingresa el nombre del cliente"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $customer->name ?? '') }}">
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="phone" class="form-label">Teléfono</label>
    <input type="tel" name="phone" id="phone" placeholder="9XX XXX XXX"
           class="form-control @error('phone') is-invalid @enderror"
           value="{{ old('phone', $customer->phone ?? '') }}">
    @error('phone')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-8">
    <label for="address" class="form-label">Dirección</label>
    <input type="text" name="address" id="address" placeholder="Ingresa la dirección"
           class="form-control @error('address') is-invalid @enderror"
           value="{{ old('address', $customer->address ?? '') }}">
    @error('address')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="d-flex gap-2 mt-4">
  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
  <a href="{{ route('customers.index') }}" class="btn btn-outline-dark">Cancelar</a>
</div>

@push('scripts')
<script>
  // Al cambiar DNI / RUC se ajusta cuántos dígitos acepta el número
  document.getElementById('document_type').addEventListener('change', e => {
    const digitos = e.target.selectedOptions[0].dataset.digitos;
    const numero = document.getElementById('document_number');
    numero.maxLength = digitos;
    numero.placeholder = digitos + ' dígitos';
  });
</script>
@endpush
