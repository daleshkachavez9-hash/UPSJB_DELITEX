{{-- Campos comunes de crear y editar una producción --}}
<div class="col-md-6">
  <label for="user_id" class="form-label">Responsable</label>
  <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
    <option value="">Seleccione el responsable</option>
    @foreach ($users as $user)
      <option value="{{ $user->id }}" @selected(old('user_id', $production?->user_id) == $user->id)>
        {{ $user->name }} ({{ $user->role->name ?? 'Sin rol' }})
      </option>
    @endforeach
  </select>
  @error('user_id')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="col-md-6">
  <label for="stage" class="form-label">Etapa</label>
  <select name="stage" id="stage" class="form-select @error('stage') is-invalid @enderror">
    @foreach ($etapas as $valor => $texto)
      <option value="{{ $valor }}" @selected(old('stage', $production?->stage ?? 'corte') === $valor)>{{ $texto }}</option>
    @endforeach
  </select>
  @error('stage')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="col-12">
  <label for="observation" class="form-label">Observación</label>
  <textarea name="observation" id="observation" rows="3" maxlength="1000"
            class="form-control @error('observation') is-invalid @enderror"
            placeholder="Ej.: usar tela dry fit azul, logo bordado en el pecho">{{ old('observation', $production?->observation) }}</textarea>
  @error('observation')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
