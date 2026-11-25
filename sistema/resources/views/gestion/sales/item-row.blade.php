{{-- Una fila del detalle de venta.
     items[0][product_id], items[1][product_id]... llegan al controlador como el arreglo $request->items.
     data-price guarda el precio para que JavaScript calcule el subtotal en pantalla. --}}
<tr data-product-id="{{ $product?->id }}">
  <td>
    <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id" value="{{ $product?->id }}">
    <span class="product-name">{{ $product?->name }}</span>
  </td>
  <td>
    <input type="number" name="items[{{ $index }}][quantity]" min="1"
           class="form-control form-control-sm text-center quantity" value="{{ $item['quantity'] ?? 1 }}">
  </td>
  <td class="text-end price" data-price="{{ $product?->price ?? 0 }}">S/ 0.00</td>
  <td class="text-end subtotal">S/ 0.00</td>
  <td class="text-center">
    <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Quitar">
      <i class="bi bi-trash"></i>
    </button>
  </td>
</tr>
