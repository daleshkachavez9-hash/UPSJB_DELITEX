<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    // Listado de entregas con el pedido, el cliente y lo pagado
    public function index()
    {
        $deliveries = Delivery::with(['order.customer'])
            ->with(['order' => fn ($q) => $q->withSum('payments', 'amount')])
            ->latest('id')
            ->get();

        return view('gestion.deliveries.index', compact('deliveries'));
    }

    // Formulario para programar una entrega
    public function create(Request $request)
    {
        // Un pedido tiene como máximo una entrega
        $orders = Order::with('customer')->withSum('payments', 'amount')->doesntHave('delivery')->orderBy('id')->get();
        $pedidoElegido = $orders->firstWhere('id', old('order_id', $request->order_id));

        return view('gestion.deliveries.create', compact('orders', 'pedidoElegido'));
    }

    // Guardar la entrega programada
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id|unique:deliveries,order_id',
            'delivery_date' => 'nullable|date',
            'observation' => 'nullable|max:500',
        ], [
            'order_id.unique' => 'Ese pedido ya tiene una entrega registrada.',
        ]);

        Delivery::create($validated + ['status' => 'pendiente']);

        return to_route('deliveries.index')->with('ok', 'Entrega programada correctamente.');
    }

    // Formulario para cambiar la fecha u observación
    public function edit(Delivery $delivery)
    {
        if ($delivery->status === 'entregado') {
            return to_route('deliveries.index')->with('error', 'Una entrega confirmada ya no se puede modificar.');
        }

        $delivery->load('order.customer');

        return view('gestion.deliveries.edit', compact('delivery'));
    }

    // Actualizar la entrega programada
    public function update(Request $request, Delivery $delivery)
    {
        if ($delivery->status === 'entregado') {
            return to_route('deliveries.index')->with('error', 'Una entrega confirmada ya no se puede modificar.');
        }

        $validated = $request->validate([
            'delivery_date' => 'nullable|date',
            'observation' => 'nullable|max:500',
        ]);

        $delivery->update($validated);

        return to_route('deliveries.index')->with('ok', 'Entrega actualizada correctamente.');
    }

    // Confirmar la entrega: solo si el pedido ya está terminado y pagado por completo
    public function confirm(Delivery $delivery)
    {
        if ($delivery->status === 'entregado') {
            return to_route('deliveries.index')->with('error', 'La entrega ya fue confirmada.');
        }

        // Método del diagrama: confirmarEntrega()
        if (! $delivery->confirmarEntrega()) {
            return to_route('deliveries.index')->with('error', 'No se puede entregar: ' . $delivery->impedimento());
        }

        return to_route('deliveries.index')->with('ok', 'Entrega confirmada. El pedido quedó como entregado.');
    }

    // Eliminar una entrega que todavía no se confirma
    public function destroy(Delivery $delivery)
    {
        if ($delivery->status === 'entregado') {
            return to_route('deliveries.index')->with('error', 'No se puede eliminar una entrega confirmada.');
        }

        $delivery->delete();

        return to_route('deliveries.index')->with('ok', 'Entrega eliminada correctamente.');
    }
}
