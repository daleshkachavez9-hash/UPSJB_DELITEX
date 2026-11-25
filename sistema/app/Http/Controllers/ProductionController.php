<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Production;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductionController extends Controller
{
    // Solo los pedidos que todavía no terminan pueden pasar por producción
    const ESTADOS_PEDIDO = ['pendiente', 'en_produccion'];

    // Listado de la producción con búsqueda por pedido o cliente y filtros por etapa y estado
    public function index(Request $request)
    {
        $productions = Production::with(['order.customer', 'user'])
            ->when($request->search, function ($query, $search) {
                $numero = (int) ltrim($search, '0');
                $query->where(function ($q) use ($search, $numero) {
                    $q->whereHas('order.customer', fn ($c) => $c->where('document_number', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%"));
                    if ($numero > 0) {
                        $q->orWhere('order_id', $numero);
                    }
                });
            })
            ->when($request->stage, fn ($query, $stage) => $query->where('stage', $stage))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->orderByRaw("FIELD(status, 'en_proceso', 'pendiente', 'terminado')")
            ->latest('id')
            ->get();

        $etapas = Production::ETAPAS;
        $estados = Production::ESTADOS;

        return view('gestion.productions.index', compact('productions', 'etapas', 'estados'));
    }

    // Formulario para registrar un trabajo de producción
    public function create(Request $request)
    {
        $orders = Order::with('customer')->whereIn('status', self::ESTADOS_PEDIDO)->orderBy('id')->get();
        $pedidoElegido = $orders->firstWhere('id', old('order_id', $request->order_id));

        return view('gestion.productions.create', array_merge($this->datosFormulario(), compact('orders', 'pedidoElegido')));
    }

    // Guardar el trabajo; si se marca "iniciar ahora" se llama a iniciarProduccion()
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', Rule::exists('orders', 'id')->whereIn('status', self::ESTADOS_PEDIDO)],
            'user_id' => 'required|exists:users,id',
            'stage' => ['required', Rule::in(array_keys(Production::ETAPAS))],
            'observation' => 'nullable|max:1000',
        ], [
            'order_id.exists' => 'El pedido no existe o ya terminó su producción.',
        ], $this->nombres());

        $production = Production::create($validated + ['status' => 'pendiente']);

        if ($request->boolean('start')) {
            $production->iniciarProduccion();

            return to_route('productions.index')->with('ok', 'Producción registrada e iniciada correctamente.');
        }

        return to_route('productions.index')->with('ok', 'Producción registrada correctamente.');
    }

    // Formulario para cambiar el responsable, la etapa o la observación
    public function edit(Production $production)
    {
        if ($production->status === 'terminado') {
            return to_route('productions.index')->with('error', 'Una producción terminada ya no se puede modificar.');
        }

        $production->load('order.customer');

        return view('gestion.productions.edit', array_merge($this->datosFormulario(), compact('production')));
    }

    // Actualizar el trabajo de producción
    public function update(Request $request, Production $production)
    {
        if ($production->status === 'terminado') {
            return to_route('productions.index')->with('error', 'Una producción terminada ya no se puede modificar.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'stage' => ['required', Rule::in(array_keys(Production::ETAPAS))],
            'start_date' => 'nullable|date|before_or_equal:today',
            'observation' => 'nullable|max:1000',
        ], [], $this->nombres());

        $production->update($validated);

        return to_route('productions.index')->with('ok', 'Producción actualizada correctamente.');
    }

    // Método del diagrama: iniciarProduccion()
    public function start(Production $production)
    {
        if ($production->status !== 'pendiente') {
            return to_route('productions.index')->with('error', 'Esta producción ya fue iniciada.');
        }

        $production->iniciarProduccion();

        return to_route('productions.index')->with('ok', 'Producción iniciada. El pedido quedó en producción.');
    }

    // Método del diagrama: actualizarAvance() (pasa a la siguiente etapa)
    public function advance(Request $request, Production $production)
    {
        if ($production->status !== 'en_proceso') {
            return to_route('productions.index')->with('error', 'Solo se puede avanzar una producción que está en proceso.');
        }

        if (! $production->siguienteEtapa()) {
            return to_route('productions.index')->with('error', 'La producción ya está en la última etapa: márquela como terminada.');
        }

        $request->validate(['observation' => 'nullable|max:500'], [], $this->nombres());
        $production->actualizarAvance($request->observation);

        return to_route('productions.index')
            ->with('ok', 'La producción pasó a la etapa ' . Production::ETAPAS[$production->stage] . '.');
    }

    // Método del diagrama: marcarTerminado()
    public function finish(Production $production)
    {
        if ($production->status !== 'en_proceso') {
            return to_route('productions.index')->with('error', 'Solo se puede terminar una producción que está en proceso.');
        }

        $production->marcarTerminado();
        $mensaje = $production->order->fresh()->status === 'terminado'
            ? 'Producción terminada. El pedido ya está listo para entregar.'
            : 'Producción terminada.';

        return to_route('productions.index')->with('ok', $mensaje);
    }

    // Eliminar un trabajo que todavía no se inicia
    public function destroy(Production $production)
    {
        if ($production->status !== 'pendiente') {
            return to_route('productions.index')
                ->with('error', 'No se puede eliminar una producción que ya fue iniciada.');
        }

        $production->delete();

        return to_route('productions.index')->with('ok', 'Producción eliminada correctamente.');
    }

    private function datosFormulario(): array
    {
        return [
            'users' => User::with('role')->orderBy('name')->get(),
            'etapas' => Production::ETAPAS,
        ];
    }

    // Nombres legibles de los campos en los mensajes de validación
    private function nombres(): array
    {
        return ['user_id' => 'responsable'];
    }
}
