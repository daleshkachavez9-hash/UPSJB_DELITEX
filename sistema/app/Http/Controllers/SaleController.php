<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    // Listado de ventas (con cliente y vendedor cargados)
    public function index()
    {
        $sales = Sale::with(['customer', 'user'])->latest('date')->get();

        return view('gestion.sales.index', compact('sales'));
    }

    // Formulario para registrar una venta directa de productos del catálogo
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::with('category')->where('status', 'activo')->where('quantity', '>', 0)->orderBy('name')->get();

        return view('gestion.sales.create', compact('customers', 'products'));
    }

    // Guardar la venta directa con sus detalles
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Debe agregar al menos un producto.',
            'items.*.product_id.distinct' => 'No repita el mismo producto, aumente la cantidad.',
        ]);

        // Transacción: si algo falla, no se guarda nada
        $sale = DB::transaction(function () use ($request) {
            // 1. Crear la venta (el total se calcula al final); el vendedor es quien inició sesión
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'user_id' => $request->user()->id,
                'date' => now(),
                'total' => 0,
                'igv' => 0,
            ]);

            // 2. Registrar cada producto vendido
            foreach ($request->items as $item) {
                // lockForUpdate(): bloquea la fila hasta terminar, para que dos ventas a la vez no usen el mismo stock
                $product = Product::lockForUpdate()->find($item['product_id']);

                if (! $product->verificarStock($item['quantity'])) {
                    // Lanzar la excepción deshace todo lo guardado en la transacción (rollback)
                    throw ValidationException::withMessages([
                        'items' => 'Stock insuficiente para ' . $product->name
                            . ' (disponible: ' . rtrim(rtrim($product->quantity, '0'), '.') . ').',
                    ]);
                }

                // El precio se toma de la base de datos, no del formulario
                $sale->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => round($product->price * $item['quantity'], 2),
                ]);

                // 3. Descontar el stock del producto y de su inventario
                $product->ajustarStock(-$item['quantity']);
            }

            // 4. Total e IGV con el método del diagrama (el precio ya incluye el 18 % de IGV)
            $sale->calcularTotal();
            $sale->save();

            return $sale;
        });

        return to_route('sales.show', $sale)->with('ok', 'Venta registrada correctamente.');
    }

    // Pedido «origina» Venta: emite el comprobante de un pedido ya entregado.
    // El cliente ya pagó con sus pagos, así que no se cobra de nuevo, y las prendas
    // salieron del taller (producción), así que no se descuenta stock.
    public function storeFromOrder(Request $request, Order $order)
    {
        if ($order->status !== 'entregado') {
            return to_route('orders.show', $order)->with('error', 'El comprobante se genera cuando el pedido ya fue entregado.');
        }

        if ($order->sale()->exists()) {
            return to_route('orders.show', $order)->with('error', 'Este pedido ya tiene su venta registrada.');
        }

        $sale = DB::transaction(function () use ($request, $order) {
            $sale = Sale::create([
                'customer_id' => $order->customer_id,
                'user_id' => $request->user()->id,
                'order_id' => $order->id,
                'date' => now(),
                'total' => 0,
                'igv' => 0,
            ]);

            // Las prendas del pedido pasan al detalle de la venta con el precio acordado
            foreach ($order->details as $detail) {
                $sale->details()->create([
                    'product_id' => $detail->product_id,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'subtotal' => $detail->subtotal,
                ]);
            }

            $sale->calcularTotal();
            $sale->save();

            return $sale;
        });

        return to_route('sales.show', $sale)->with('ok', 'Venta del pedido registrada. El monto ya estaba cobrado con los pagos del pedido.');
    }

    // Ver el detalle de una venta
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'order', 'details.product']);

        return view('gestion.sales.show', compact('sale'));
    }

    // Anular venta: devuelve el stock (solo en ventas directas) y la elimina
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // La venta de un pedido no descontó stock, así que tampoco lo devuelve
            if (! $sale->order_id) {
                foreach ($sale->details as $detail) {
                    Product::lockForUpdate()->find($detail->product_id)->ajustarStock($detail->quantity);
                }
            }

            // Los detalles se borran solos (cascadeOnDelete en la migración)
            $sale->delete();
        });

        return to_route('sales.index')->with('ok', $sale->order_id ? 'Venta del pedido anulada.' : 'Venta anulada y stock devuelto.');
    }
}
