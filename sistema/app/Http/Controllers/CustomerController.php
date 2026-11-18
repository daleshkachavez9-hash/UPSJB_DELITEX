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
        //
    }

    // CREATE: validar, guardar, redirigir
    public function store(Request $request)
    {
        //
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
}
