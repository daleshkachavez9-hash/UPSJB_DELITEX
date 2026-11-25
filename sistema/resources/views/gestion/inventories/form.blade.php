{{-- Formulario compartido por create y edit --}}
<div class="row g-3">
  <div class="col-md-6">
    <label for="product_id" class="form-label">Producto</label>
    {{-- Clave foránea: se guarda product_id, se muestra el nombre del producto --}}
    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
      <option value="">-- Seleccione --</option>
      @foreach ($products as $product)
        <option value="{{ $product->id }}" data-cantidad="{{ (float) $product->quantity }}"
                @selected(old('product_id', $inventory->product_id ?? '') == $product->id)>
          {{ $product->name }} ({{ $product->unit }})
        </option>
      @endforeach
    </select>
    @error('product_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6">
    <label for="last_update" class="form-label">Fecha de actualización <small class="text-muted">(si se deja vacía, se usa la fecha de hoy)</small></label>
    <input type="date" name="last_update" id="last_update"
           class="form-control @error('last_update') is-invalid @enderror"
           value="{{ old('last_update', isset($inventory) ? $inventory->last_update?->format('Y-m-d') : '') }}">
    @error('last_update')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6">
    <label for="current_stock" class="form-label">Stock actual</label>
    <input type="number" name="current_stock" id="current_stock" step="0.01" min="0"
           class="form-control @error('current_stock') is-invalid @enderror"
           value="{{ old('current_stock', $inventory->current_stock ?? 0) }}">
    @error('current_stock')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Al guardar, la cantidad del producto se actualiza con este stock.</small>
  </div>

  <div class="col-md-6">
    <label for="minimum_stock" class="form-label">Stock mínimo</label>
    <input type="number" name="minimum_stock" id="minimum_stock" step="0.01" min="0"
           class="form-control @error('minimum_stock') is-invalid @enderror"
           value="{{ old('minimum_stock', $inventory->minimum_stock ?? 0) }}">
    @error('minimum_stock')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="d-flex gap-2 mt-4">
  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
  <a href="{{ route('inventories.index') }}" class="btn btn-outline-dark">Cancelar</a>
</div>

@push('scripts')
<script>
  // Al elegir el producto se propone su cantidad actual como stock
  document.getElementById('product_id').addEventListener('change', e => {
    const opcion = e.target.selectedOptions[0];
    if (opcion.dataset.cantidad !== undefined) {
      document.getElementById('current_stock').value = opcion.dataset.cantidad;
    }
  });
</script>
@endpush
