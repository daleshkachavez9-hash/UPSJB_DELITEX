{{-- Plantilla de fila, ventanas de búsqueda y JavaScript del formulario de pedidos --}}
<template id="item-template">
  @include('gestion.orders.item-row', ['index' => '__INDEX__', 'item' => [], 'product' => null])
</template>

@include('partials.modal-buscar', [
  'id' => 'modalCliente',
  'titulo' => 'Buscar cliente',
  'placeholder' => 'Escriba el DNI o RUC, o el nombre del cliente',
  'columnas' => ['DNI', 'Nombre', 'Teléfono'],
  'filas' => $customers->map(fn ($c) => [
    'id' => $c->id,
    'texto' => $c->document_number . ' ' . $c->name,
    'celdas' => [$c->documento(), $c->name, $c->phone ?? '—'],
    'datos' => ['label' => $c->name . ' - ' . $c->documento()],
  ])->all(),
])

@include('partials.modal-buscar', [
  'id' => 'modalProducto',
  'titulo' => 'Agregar producto del catálogo',
  'placeholder' => 'Escriba el nombre o la categoría del producto',
  'columnas' => ['Producto', 'Categoría', 'Precio'],
  'filas' => $products->map(fn ($p) => [
    'id' => $p->id,
    'texto' => $p->name . ' ' . $p->category->name,
    'celdas' => [$p->name, $p->category->name, 'S/ ' . number_format($p->price, 2)],
    'datos' => ['name' => $p->name, 'price' => $p->price],
  ])->all(),
])

@push('scripts')
<script src="{{ asset('js/buscador.js') }}"></script>
<script>
  const tbody = document.getElementById('items');
  const template = document.getElementById('item-template').innerHTML;
  let index = {{ count(old('items', isset($order) ? $order->details : [])) }};

  // Recalcula subtotales y total
  function recalculate() {
    let total = 0;
    tbody.querySelectorAll('tr').forEach(row => {
      const price = parseFloat(row.querySelector('.unit-price').value || 0);
      const quantity = parseInt(row.querySelector('.quantity').value || 0);
      const subtotal = price * quantity;
      row.querySelector('.subtotal').textContent = 'S/ ' + subtotal.toFixed(2);
      total += subtotal;
    });
    document.getElementById('total').textContent = 'S/ ' + total.toFixed(2);
    document.getElementById('sin-items').classList.toggle('d-none', tbody.children.length > 0);
  }

  // Agrega una fila vacía y la devuelve
  function nuevaFila() {
    tbody.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index++));
    return tbody.lastElementChild;
  }

  // Cliente elegido en la ventana de búsqueda
  document.getElementById('modalCliente').addEventListener('buscador:elegido', e => {
    document.getElementById('customer_id').value = e.detail.id;
    document.getElementById('customer_label').value = e.detail.label;
  });

  // Producto del catálogo: se agrega con su precio, que se puede ajustar
  document.getElementById('modalProducto').addEventListener('buscador:elegido', e => {
    const fila = nuevaFila();
    fila.querySelector('.product-id').value = e.detail.id;
    fila.querySelector('.product-name').textContent = e.detail.name;
    fila.querySelector('.unit-price').value = e.detail.price;
    recalculate();
  });

  // Prenda personalizada: sin producto del catálogo
  document.getElementById('add-custom').addEventListener('click', () => {
    nuevaFila().querySelector('.unit-price').focus();
    recalculate();
  });

  tbody.addEventListener('click', e => {
    if (e.target.closest('.remove-item')) {
      e.target.closest('tr').remove();
      recalculate();
    }
  });

  tbody.addEventListener('input', recalculate);
  recalculate();
</script>
@endpush
