{{-- Una fila del detalle del pedido: un producto del catálogo o una prenda personalizada (sin producto).
     items[0][...], items[1][...] llegan al controlador como el arreglo $request->items. --}}
<tr>
  <td>
    <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id" value="{{ $product?->id }}">
    <span class="product-name">{{ $product?->name ?? 'Prenda personalizada' }}</span>
  </td>
  <td>
    <input type="text" name="items[{{ $index }}][size]" maxlength="20" placeholder="Ej. M"
           class="form-control form-control-sm" value="{{ $item['size'] ?? '' }}">
  </td>
  <td>
    <input type="text" name="items[{{ $index }}][color]" maxlength="30" placeholder="Ej. Azul"
           class="form-control form-control-sm" value="{{ $item['color'] ?? '' }}">
  </td>
  <td>
    <input type="number" name="items[{{ $index }}][quantity]" min="1"
           class="form-control form-control-sm text-center quantity" value="{{ $item['quantity'] ?? 1 }}">
  </td>
  <td>
    <input type="number" name="items[{{ $index }}][unit_price]" min="0" step="0.01"
           class="form-control form-control-sm text-end unit-price" value="{{ $item['unit_price'] ?? '' }}">
  </td>
  <td class="text-end subtotal">S/ 0.00</td>
  <td class="text-center">
    <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Quitar">
      <i class="bi bi-trash"></i>
    </button>
  </td>
</tr>
