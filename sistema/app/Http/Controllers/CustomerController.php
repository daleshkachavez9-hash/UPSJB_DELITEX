<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate($this->reglas($request), $this->mensajes($request));

        Customer::create($validated);

        return to_route('customers.index')
            ->with('ok', 'Cliente registrado correctamente.');
    }

    // Formulario lleno (route model binding)
    public function edit(Customer $customer)
    {
        return view('gestion.customers.edit', compact('customer'));
    }

    // UPDATE: validar, actualizar, redirigir
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate($this->reglas($request, $customer), $this->mensajes($request));

        $customer->update($validated);

        return to_route('customers.index')
            ->with('ok', 'Cliente actualizado correctamente.');
    }

    // DELETE: respeta restrictOnDelete de orders y sales
    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists() || $customer->sales()->exists()) {
            return to_route('customers.index')
                ->with('error', 'No se puede eliminar: el cliente tiene pedidos o ventas registrados.');
        }

        $customer->delete();

        return to_route('customers.index')
            ->with('ok', 'Cliente eliminado correctamente.');
    }

    // El número de dígitos depende del tipo de documento elegido (DNI 8, RUC 11)
    private function reglas(Request $request, ?Customer $customer = null): array
    {
        $digitos = Customer::DOCUMENTOS[$request->document_type] ?? 8;
        $numero = ['required', "digits:$digitos", Rule::unique('customers', 'document_number')->ignore($customer?->id)];
        if ($request->document_type === 'RUC') {
            // El RUC empieza con 10 (persona con negocio) o con 15, 16, 17 o 20 (empresas y otras entidades)
            $numero[] = 'regex:/^(10|15|16|17|20)/';
        }

        return [
            'document_type' => ['required', Rule::in(array_keys(Customer::DOCUMENTOS))],
            'document_number' => $numero,
            'name' => 'required|max:150',
            'phone' => 'nullable|max:15',
            'address' => 'nullable|max:255',
        ];
    }

    private function mensajes(Request $request): array
    {
        $tipo = $request->document_type === 'RUC' ? 'RUC' : 'DNI';

        return [
            'name.required' => 'Escribe el nombre o la razón social del cliente.',
            'name.max' => 'El nombre no debe superar los 150 caracteres.',
            'document_type.required' => 'Elige si el documento es DNI o RUC.',
            'document_type.in' => 'El tipo de documento debe ser DNI o RUC.',
            'document_number.required' => "Escribe el $tipo del cliente.",
            'document_number.digits' => "El $tipo debe tener " . Customer::DOCUMENTOS[$tipo] . ' dígitos.',
            'document_number.regex' => 'El RUC debe empezar con 10, 15, 16, 17 o 20.',
            'document_number.unique' => 'Ese número de documento ya está registrado.',
            'phone.max' => 'El teléfono no debe superar los 15 caracteres.',
            'address.max' => 'La dirección no debe superar los 255 caracteres.',
        ];
    }
}
