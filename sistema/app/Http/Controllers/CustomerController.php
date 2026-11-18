<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // READ: listado
    public function index()
    {
        $customers = Customer::all();

        return view('gestion.customers.index', compact('customers'));
    }

    // Formulario vacío
    public function create()
    {
        return view('gestion.customers.create');
    }

    // CREATE: validar, guardar, redirigir
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:150',
            'dni' => 'required|digits:8|unique:customers',
            'phone' => 'nullable|max:15',
            'address' => 'nullable|max:255',
        ], $this->mensajes());

        Customer::create($validated);

        return to_route('customers.index')
            ->with('ok', 'Cliente registrado correctamente.');
    }

    // Formulario lleno (route model binding)
    public function edit(Customer $customer)
    {
        //
    }

    // UPDATE: validar, actualizar, redirigir
    public function update(Request $request, Customer $customer)
    {
        //
    }

    // DELETE: respeta restrictOnDelete de orders y sales
    public function destroy(Customer $customer)
    {
        //
    }

    private function mensajes(): array
    {
        return [
            'name.required' => 'Escribe el nombre del cliente.',
            'name.max' => 'El nombre no debe superar los 150 caracteres.',
            'dni.required' => 'Escribe el DNI del cliente.',
            'dni.digits' => 'El DNI debe tener 8 dígitos.',
            'dni.unique' => 'Ese DNI ya está registrado.',
            'phone.max' => 'El teléfono no debe superar los 15 caracteres.',
            'address.max' => 'La dirección no debe superar los 255 caracteres.',
        ];
    }
}
