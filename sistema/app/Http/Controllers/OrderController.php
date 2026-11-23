<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    // Listado de pedidos con búsqueda por número, DNI o nombre y filtro por estado
    public function index(Request $request)
    {
        $orders = Order::with('customer')
            ->withSum('payments', 'amount')
            ->when($request->search, function ($query, $search) {
                $numero = (int) ltrim($search, '0');
                $query->where(function ($q) use ($search, $numero) {
                    $q->whereHas('customer', fn ($c) => $c->where('dni', 'like', "%{$search}%")
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

        $order = DB::transaction(function () use ($validated) {
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'description' => $validated['description'] ?? null,
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
        $order->load(['customer', 'details.product', 'payments', 'delivery', 'sale']);
        $pagado = (float) $order->payments->sum('amount');
        $saldo = round((float) $order->total - $pagado, 2);
        $estados = Order::ESTADOS;

        return view('gestion.orders.show', compact('order', 'pagado', 'saldo', 'estados'));
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
        });

        return to_route('orders.show', $order)->with('ok', 'Pedido actualizado correctamente.');
    }

    // Cambiar la etapa del pedido (método del diagrama: actualizarEstado)
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            // "entregado" solo se asigna al confirmar la entrega
            'status' => ['required', Rule::in(array_diff(array_keys(Order::ESTADOS), ['entregado']))],
        ]);

        if ($order->status === 'entregado') {
            return to_route('orders.show', $order)->with('error', 'El pedido ya fue entregado.');
        }

        $order->actualizarEstado($request->status);

        return to_route('orders.show', $order)->with('ok', 'Estado del pedido actualizado.');
    }

    // Eliminar pedido (solo si no tiene pagos, entrega ni venta)
    public function destroy(Order $order)
    {
        if ($order->payments()->exists() || $order->delivery()->exists() || $order->sale()->exists()) {
            return to_route('orders.index')
                ->with('error', 'No se puede eliminar el pedido porque tiene pagos, entrega o venta registrados.');
        }

        // Los detalles se borran solos (cascadeOnDelete en la migración)
        $order->delete();

        return to_route('orders.index')->with('ok', 'Pedido eliminado correctamente.');
    }

    // Crea las filas del detalle y guarda el total con el método del diagrama
    private function guardarDetalles(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $order->details()->create([
                'product_id' => $item['product_id'] ?? null,
                'quantity' => $item['quantity'],
                'size' => $item['size'] ?? null,
                'color' => $item['color'] ?? null,
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
            'products' => Product::with('category')->where('status', 'activo')->orderBy('name')->get(),
        ];
    }

    private function reglas(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'description' => 'nullable|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.size' => 'nullable|max:20',
            'items.*.color' => 'nullable|max:30',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    private function mensajes(): array
    {
        return [
            'items.required' => 'Debe agregar al menos una prenda al pedido.',
        ];
    }
}
