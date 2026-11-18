{{-- Formulario compartido por create.blade.php y edit.blade.php --}}
{{-- En edit existe $customer; en create no, y ?? '' deja el campo vacío --}}
<div class="row g-3">
  <div class="col-md-4">
    <label for="dni" class="form-label">DNI</label>
    <input type="text" name="dni" id="dni" maxlength="8" inputmode="numeric" placeholder="8 dígitos"
           class="form-control @error('dni') is-invalid @enderror"
           value="{{ old('dni', $customer->dni ?? '') }}">
    @error('dni')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-8">
    <label for="name" class="form-label">Nombres y apellidos</label>
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
