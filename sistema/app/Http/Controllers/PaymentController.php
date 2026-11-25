<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    // Formas de pago que acepta el taller
    const TIPOS = ['Efectivo', 'Yape', 'Plin', 'Transferencia', 'Tarjeta'];

    // Listado de pagos (con el pedido y su cliente cargados)
    public function index()
    {
        $payments = Payment::with('order.customer')->latest('payment_date')->latest('id')->get();

        return view('gestion.payments.index', compact('payments'));
    }

    // Formulario para registrar un pago
    public function create(Request $request)
    {
        // Solo pedidos con saldo pendiente
        $orders = Order::with('customer')->withSum('payments', 'amount')
            ->where('status', '!=', 'entregado')
            ->get()
            ->filter(fn ($o) => (float) $o->total - (float) $o->payments_sum_amount > 0)
            ->sortBy('id')
            ->values();

        $pedidoElegido = $orders->firstWhere('id', old('order_id', $request->order_id));
        $tipos = self::TIPOS;

        return view('gestion.payments.create', compact('orders', 'pedidoElegido', 'tipos'));
    }

    // Guardar el pago (nunca mayor al saldo del pedido)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_type' => ['required', Rule::in(self::TIPOS)],
        ]);

        DB::transaction(function () use ($validated) {
            // Bloquea el pedido para que dos pagos a la vez no superen el saldo
            $order = Order::lockForUpdate()->find($validated['order_id']);

            if ($order->status === 'entregado') {
                throw ValidationException::withMessages(['order_id' => 'El pedido ya fue entregado.']);
            }

            // El pago no puede ser de antes de que existiera el pedido
            if ($validated['payment_date'] < $order->order_date->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'payment_date' => 'La fecha de pago no puede ser anterior a la fecha del pedido (' . $order->order_date->format('d/m/Y') . ').',
                ]);
            }

            $payment = $order->payments()->create($validated);

            // Método del diagrama: calcularSaldo()
            $saldo = $payment->calcularSaldo();
            if ($saldo < 0) {
                throw ValidationException::withMessages([
                    'amount' => 'El monto supera el saldo del pedido (S/ ' . number_format($saldo + $validated['amount'], 2) . ').',
                ]);
            }

            // Adelanto o cancelación según lo pagado hasta ahora
            $order->actualizarEstadoPagos();
        });

        return to_route('orders.show', $validated['order_id'])->with('ok', 'Pago registrado correctamente.');
    }

    // Anular pago (no se permite si el pedido ya se entregó)
    public function destroy(Payment $payment)
    {
        if ($payment->order->status === 'entregado') {
            return to_route('payments.index')
                ->with('error', 'No se puede anular el pago porque el pedido ya fue entregado.');
        }

        $order = $payment->order;
        $payment->delete();
        // Si se anula un pago, el que antes cancelaba el pedido puede volver a ser un adelanto
        $order->actualizarEstadoPagos();

        return to_route('payments.index')->with('ok', 'Pago anulado correctamente.');
    }
}
