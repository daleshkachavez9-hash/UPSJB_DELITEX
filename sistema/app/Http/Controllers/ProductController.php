<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // Cantidad a partir de la cual el stock se considera bajo
    const LOW_STOCK = 10;

    // Unidades de medida de prendas e insumos
    const UNITS = ['unidad', 'metro', 'cono', 'paquete', 'rollo'];

    // Listado de productos con búsqueda y filtros
    public function index(Request $request)
    {
        $products = Product::with('category')                       // carga la categoría en 1 sola consulta (evita N+1)
            ->when($request->search, function ($query, $search) {  // solo si escribió algo en el buscador
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) { // solo si eligió una categoría
                $query->where('category_id', $categoryId);
            })
            ->when($request->boolean('low_stock'), function ($query) {     // solo si marcó "stock bajo"
                $query->where('quantity', '<=', self::LOW_STOCK);
            })
            ->orderBy('name')
            ->get();

        // Para llenar el <select> del filtro
        $categories = Category::orderBy('name')->get();
        $lowStock = self::LOW_STOCK;

        return view('gestion.products.index', compact('products', 'categories', 'lowStock'));
    }

    // Formulario para crear
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = self::UNITS;

        return view('gestion.products.create', compact('categories', 'units'));
    }

    // Guardar nuevo producto
    public function store(Request $request)
    {
        $validated = $request->validate($this->reglas());

        // Si subió una imagen, se guarda en storage/app/public/products
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return to_route('products.index')->with('ok', 'Producto registrado correctamente.');
    }

    // Formulario para editar
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $units = self::UNITS;

        return view('gestion.products.edit', compact('product', 'categories', 'units'));
    }

    // Actualizar producto
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate($this->reglas());

        if ($request->hasFile('image')) {
            // Borra la imagen anterior y guarda la nueva
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            // Si no subió imagen nueva, se mantiene la actual
            unset($validated['image']);
        }

        $product->update($validated);

        return to_route('products.index')->with('ok', 'Producto actualizado correctamente.');
    }

    // Eliminar producto (solo si no se ha vendido, no está en pedidos ni tiene inventario)
    public function destroy(Product $product)
    {
        if ($product->detailSales()->exists() || $product->orderDetails()->exists() || $product->inventories()->exists()) {
            return to_route('products.index')
                ->with('error', 'No se puede eliminar el producto porque tiene ventas, pedidos o registros de inventario.');
        }

        // Borra también su imagen
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return to_route('products.index')->with('ok', 'Producto eliminado correctamente.');
    }

    private function reglas(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:150',
            'garment_type' => 'required|max:50',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'unit' => ['required', Rule::in(self::UNITS)],
            'status' => 'required|in:activo,inactivo',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // máx. 2 MB
        ];
    }
}
