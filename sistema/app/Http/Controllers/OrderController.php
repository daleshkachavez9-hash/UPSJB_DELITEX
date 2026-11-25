<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    // Listado de pedidos con búsqueda por número, DNI o RUC, o nombre y filtro por estado
    public function index(Request $request)
    {
        $orders = Order::with('customer')
            ->withSum('payments', 'amount')
            ->when($request->search, function ($query, $search) {
                $numero = (int) ltrim($search, '0');
                $query->where(function ($q) use ($search, $numero) {
                    $q->whereHas('customer', fn ($c) => $c->where('document_number', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%"));
                    if ($numero > 0) {
                        $q->orWhere('id', $numero);
                    }
                });
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest('order_date')
            ->latest('id')
            ->get();

        $estados = Order::ESTADOS;

        return view('gestion.orders.index', compact('orders', 'estados'));
    }

    // Formulario para registrar un pedido
    public function create()
    {
        return view('gestion.orders.create', $this->datosFormulario());
    }

    // Guardar el pedido con su detalle
    public function store(Request $request)
    {
        $validated = $request->validate($this->reglas(), $this->mensajes());

        // Imagen de referencia (logo, escudo o diseño): se guarda en storage/app/public/orders
        $imagen = $request->hasFile('reference_image') ? $request->file('reference_image')->store('orders', 'public') : null;

        $order = DB::transaction(function () use ($validated, $imagen) {
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'description' => $validated['description'] ?? null,
                'reference_image' => $imagen,
                'status' => 'pendiente',
                'total' => 0,
            ]);

            $this->guardarDetalles($order, $validated['items']);

            return $order;
        });

        return to_route('orders.show', $order)->with('ok', 'Pedido registrado correctamente.');
    }

    // Ver el pedido con su detalle, pagos y entrega
    public function show(Order $order)
    {
        $order->load(['customer', 'details.product', 'payments', 'delivery', 'sale', 'productions.user']);
        $pagado = (float) $order->payments->sum('amount');
        $saldo = round((float) $order->total - $pagado, 2);

        return view('gestion.orders.show', compact('order', 'pagado', 'saldo'));
    }

    // Ficha de producción del pedido (para imprimir o guardar en PDF desde el navegador)
    public function ficha(Order $order)
    {
        $order->load(['customer', 'details.product', 'delivery', 'productions.user']);

        // Las filas de la misma prenda (mismo tipo, color, tela y estampado) se juntan en un bloque
        // con su cuadro de tallas, como en la ficha del prototipo
        $orden = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $prendas = $order->details
            ->groupBy(fn ($d) => implode('|', [$d->product_id, $d->tipoPrenda(), mb_strtolower((string) $d->color), $d->fabric, $d->print_type, $d->print_detail, $d->notes]))
            ->map(function ($filas) use ($orden) {
                $tallas = $filas->groupBy(fn ($d) => $d->size ?: 'Única')
                    ->map(fn ($g) => $g->sum('quantity'))
                    ->sortBy(fn ($q, $talla) => array_search(mb_strtoupper($talla), $orden) !== false
                        ? array_search(mb_strtoupper($talla), $orden) : 100 + (int) $talla);

                return ['detalle' => $filas->first(), 'tallas' => $tallas, 'cantidad' => $filas->sum('quantity')];
            })
            ->values();

        return view('gestion.orders.ficha', compact('order', 'prendas'));
    }

    // Formulario para modificar un pedido personalizado
    public function edit(Order $order)
    {
        if ($order->status === 'entregado') {
            return to_route('orders.show', $order)->with('error', 'Un pedido entregado ya no se puede modificar.');
        }

        $order->load('details.product');

        return view('gestion.orders.edit', array_merge($this->datosFormulario(), compact('order')));
    }

    // Actualizar la cabecera y reemplazar el detalle del pedido
    public function update(Request $request, Order $order)
    {
        if ($order->status === 'entregado') {
            return to_route('orders.show', $order)->with('error', 'Un pedido entregado ya no se puede modificar.');
        }

        $validated = $request->validate($this->reglas(), $this->mensajes());

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'description' => $validated['description'] ?? null,
            ]);

            $order->details()->delete();
            $this->guardarDetalles($order, $validated['items']);

            // El nuevo total no puede ser menor a lo que el cliente ya pagó
            $pagado = (float) $order->payments()->sum('amount');
            if ((float) $order->total < $pagado) {
                throw ValidationException::withMessages([
                    'items' => 'El total del pedido no puede ser menor a lo ya pagado (S/ ' . number_format($pagado, 2) . ').',
                ]);
            }

            // Con el nuevo total, un pago que antes era la cancelación puede pasar a ser un adelanto
            $order->actualizarEstadoPagos();
        });

        // Imagen de referencia: si sube una nueva se borra la anterior; también se puede quitar
        if ($request->hasFile('reference_image') || $request->boolean('remove_image')) {
            if ($order->reference_image) {
                Storage::disk('public')->delete($order->reference_image);
            }
            $order->update([
                'reference_image' => $request->hasFile('reference_image') ? $request->file('reference_image')->store('orders', 'public') : null,
            ]);
        }

        return to_route('orders.show', $order)->with('ok', 'Pedido actualizado correctamente.');
    }

    // Eliminar pedido (solo si no tiene pagos, entrega, venta ni producción)
    public function destroy(Order $order)
    {
        if ($order->payments()->exists() || $order->delivery()->exists() || $order->sale()->exists() || $order->productions()->exists()) {
            return to_route('orders.index')
                ->with('error', 'No se puede eliminar el pedido porque tiene pagos, entrega, venta o producción registrados.');
        }

        // Los detalles se borran solos (cascadeOnDelete en la migración); la imagen se borra del disco
        if ($order->reference_image) {
            Storage::disk('public')->delete($order->reference_image);
        }
        $order->delete();

        return to_route('orders.index')->with('ok', 'Pedido eliminado correctamente.');
    }

    // Crea las filas del detalle y guarda el total con el método del diagrama
    private function guardarDetalles(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $order->details()->create([
                'product_id' => $item['product_id'] ?? null,
                // Si es del catálogo, el tipo de prenda sale del producto
                'garment_type' => empty($item['product_id']) ? $item['garment_type'] : null,
                'quantity' => $item['quantity'],
                'size' => $item['size'] ?? null,
                'color' => $item['color'] ?? null,
                'fabric' => $item['fabric'] ?? null,
                // Si no se indica, la prenda va sin estampado
                'print_type' => $item['print_type'] ?? 'Sin estampado',
                'print_detail' => ($item['print_type'] ?? 'Sin estampado') === 'Sin estampado' ? null : ($item['print_detail'] ?? null),
                'notes' => $item['notes'] ?? null,
                'unit_price' => $item['unit_price'],
                'subtotal' => round($item['quantity'] * $item['unit_price'], 2),
            ]);
        }

        $order->calcularTotal();
        $order->save();
    }

    private function datosFormulario(): array
    {
        return [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::with('category')->prendas()->where('status', 'activo')->orderBy('name')->get(),
        ];
    }

    private function reglas(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|max:1000',
            'reference_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // máx. 2 MB
            'items' => 'required|array|min:1',
            // Prenda del catálogo (opcional); los insumos no se piden
            'items.*.product_id' => ['nullable', Rule::exists('products', 'id')->where(fn ($q) => $q->whereIn('category_id', Category::where('type', 'prenda')->select('id')))],
            // Una prenda personalizada necesita su tipo (camiseta, short, buzo...) para la ficha
            'items.*.garment_type' => ['required_without:items.*.product_id', 'nullable', Rule::in(OrderDetail::PRENDAS)],
            'items.*.size' => 'nullable|max:20',
            'items.*.color' => 'nullable|max:30',
            'items.*.fabric' => ['nullable', Rule::in(OrderDetail::TELAS)],
            'items.*.print_type' => ['nullable', Rule::in(OrderDetail::ESTAMPADOS)],
            'items.*.print_detail' => 'nullable|max:150',
            'items.*.notes' => 'nullable|max:200',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    private function mensajes(): array
    {
        return [
            'items.required' => 'Debe agregar al menos una prenda al pedido.',
            'items.*.product_id.exists' => 'Solo se piden prendas; los insumos son de uso del taller.',
            'items.*.garment_type.required_without' => 'Elija el tipo de cada prenda personalizada.',
        ];
    }
}
