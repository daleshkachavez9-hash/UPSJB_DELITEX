{{-- Formulario compartido por create y edit --}}
<div class="row g-3">
  <div class="col-md-6">
    <label for="name" class="form-label">Nombre del producto</label>
    <input type="text" name="name" id="name" maxlength="150" placeholder="Ej. Polo deportivo dry-fit"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $product->name ?? '') }}">
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6">
    <label for="category_id" class="form-label">Categoría</label>
    {{-- Se guarda el id de la categoría (clave foránea), pero se muestra su nombre --}}
    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
      <option value="">-- Seleccione --</option>
      @foreach ($categories as $category)
        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
          {{ $category->name }}
        </option>
      @endforeach
    </select>
    @error('category_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="garment_type" class="form-label">Tipo de prenda o insumo</label>
    <input type="text" name="garment_type" id="garment_type" maxlength="50" placeholder="Ej. Polo, Short, Tela"
           class="form-control @error('garment_type') is-invalid @enderror"
           value="{{ old('garment_type', $product->garment_type ?? '') }}">
    @error('garment_type')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="price" class="form-label">Precio (S/)</label>
    <input type="number" name="price" id="price" step="0.01" min="0"
           class="form-control @error('price') is-invalid @enderror"
           value="{{ old('price', $product->price ?? '') }}">
    @error('price')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="quantity" class="form-label">Stock</label>
    <input type="number" name="quantity" id="quantity" step="0.01" min="0"
           class="form-control @error('quantity') is-invalid @enderror"
           value="{{ old('quantity', $product->quantity ?? 0) }}">
    @error('quantity')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="unit" class="form-label">Unidad de medida</label>
    <select name="unit" id="unit" class="form-select @error('unit') is-invalid @enderror">
      @foreach ($units as $unit)
        <option value="{{ $unit }}" @selected(old('unit', $product->unit ?? 'unidad') === $unit)>{{ ucfirst($unit) }}</option>
      @endforeach
    </select>
    @error('unit')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="status" class="form-label">Estado</label>
    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
      <option value="activo" @selected(old('status', $product->status ?? 'activo') === 'activo')>Activo</option>
      <option value="inactivo" @selected(old('status', $product->status ?? 'activo') === 'inactivo')>Inactivo</option>
    </select>
    @error('status')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-12">
    <label for="image" class="form-label">
      Imagen <small class="text-muted">(opcional, jpg/png/webp, máx. 2 MB)</small>
    </label>
    {{-- type="file" solo funciona si el <form> tiene enctype="multipart/form-data" --}}
    <input type="file" name="image" id="image" accept="image/*"
           class="form-control @error('image') is-invalid @enderror">
    @error('image')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    {{-- Al editar, muestra la imagen actual. asset('storage/...') funciona gracias a storage:link --}}
    @if (!empty($product?->image))
      <div class="mt-2">
        <small class="text-muted d-block mb-1">Imagen actual:</small>
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
             class="img-thumbnail" style="max-height: 120px">
      </div>
    @endif
  </div>
</div>

<div class="d-flex gap-2 mt-4">
  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
  <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Cancelar</a>
</div>
