<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // Listado de categorías con la cantidad de productos de cada una
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('settings.categories.index', compact('categories'));
    }

    // Formulario para crear
    public function create()
    {
        return view('settings.categories.create');
    }

    // Guardar nueva categoría
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:categories',
            'description' => 'nullable|max:255',
        ]);

        Category::create($validated);

        return to_route('categories.index')->with('ok', 'Categoría registrada correctamente.');
    }

    // Formulario para editar
    public function edit(Category $category)
    {
        return view('settings.categories.edit', compact('category'));
    }

    // Actualizar categoría (el nombre sigue siendo único, ignorando la propia)
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:50', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|max:255',
        ]);

        $category->update($validated);

        return to_route('categories.index')->with('ok', 'Categoría actualizada correctamente.');
    }

    // Eliminar categoría (solo si no tiene productos)
    public function destroy(Category $category)
    {
        // La FK es restrictOnDelete(): se avisa antes de que MySQL lance el error 1451
        if ($category->products()->exists()) {
            return to_route('categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $category->delete();

        return to_route('categories.index')->with('ok', 'Categoría eliminada correctamente.');
    }
}
