<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
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

    // Formulario para registrar una venta
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $products = Product::with('category')->where('status', 'activo')->where('quantity', '>', 0)->orderBy('name')->get();
        // Pedidos que todavía no originaron una venta (Pedido 1 — 0..1 Venta)
        $orders = Order::with('customer')->doesntHave('sale')->latest('order_date')->get();

        return view('gestion.sales.create', compact('customers', 'users', 'products', 'orders'));
    }

    // Guardar la venta con sus detalles
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id|unique:sales,order_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Debe agregar al menos un producto.',
            'items.*.product_id.distinct' => 'No repita el mismo producto, aumente la cantidad.',
            'order_id.unique' => 'Ese pedido ya tiene una venta registrada.',
        ]);

        // Transacción: si algo falla, no se guarda nada
        $sale = DB::transaction(function () use ($request) {
            // 1. Crear la venta (el total se calcula al final)
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'user_id' => $request->user_id,
                'order_id' => $request->order_id,
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

    // Ver el detalle de una venta
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'order', 'details.product']);

        return view('gestion.sales.show', compact('sale'));
    }

    // Anular venta: devuelve el stock y la elimina
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->details as $detail) {
                Product::lockForUpdate()->find($detail->product_id)->ajustarStock($detail->quantity);
            }

            // Los detalles se borran solos (cascadeOnDelete en la migración)
            $sale->delete();
        });

        return to_route('sales.index')->with('ok', 'Venta anulada y stock devuelto.');
    }
}
