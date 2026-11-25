<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    // Listado del inventario; con ?alerts=1 muestra solo lo que está bajo el stock mínimo
    public function index(Request $request)
    {
        $inventories = Inventory::with('product.category')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->when($request->boolean('alerts'), function ($query) {
                $query->whereColumn('current_stock', '<=', 'minimum_stock');
            })
            ->get()
            ->sortBy('product.name');

        return view('gestion.inventories.index', compact('inventories'));
    }

    // Formulario para crear (solo productos que todavía no tienen inventario)
    public function create()
    {
        $products = Product::doesntHave('inventory')->orderBy('name')->get();

        return view('gestion.inventories.create', compact('products'));
    }

    // Guardar nuevo registro de inventario
    public function store(Request $request)
    {
        $validated = $request->validate($this->reglas(), $this->mensajes());
        $validated['last_update'] = $validated['last_update'] ?? now();

        $inventory = Inventory::create($validated);
        $this->sincronizarProducto($inventory);

        return to_route('inventories.index')->with('ok', 'Inventario registrado correctamente.');
    }

    // Formulario para editar
    public function edit(Inventory $inventory)
    {
        $products = Product::where(function ($q) use ($inventory) {
            $q->doesntHave('inventory')->orWhere('id', $inventory->product_id);
        })->orderBy('name')->get();

        return view('gestion.inventories.edit', compact('inventory', 'products'));
    }

    // Actualizar inventario
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate($this->reglas($inventory), $this->mensajes());
        $validated['last_update'] = $validated['last_update'] ?? now();

        $inventory->update($validated);
        $this->sincronizarProducto($inventory);

        return to_route('inventories.index')->with('ok', 'Inventario actualizado correctamente.');
    }

    // Eliminar registro de inventario
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return to_route('inventories.index')->with('ok', 'Registro de inventario eliminado correctamente.');
    }

    // El stock actual registrado en el inventario pasa a ser la cantidad del producto
    private function sincronizarProducto(Inventory $inventory): void
    {
        $inventory->product->update(['quantity' => $inventory->current_stock]);
    }

    private function reglas(?Inventory $inventory = null): array
    {
        return [
            // Cada producto tiene un solo registro de inventario
            'product_id' => ['required', 'exists:products,id', Rule::unique('inventories')->ignore($inventory?->id)],
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'last_update' => 'nullable|date',
        ];
    }

    private function mensajes(): array
    {
        return [
            'product_id.unique' => 'Ese producto ya tiene un registro de inventario.',
        ];
    }
}
