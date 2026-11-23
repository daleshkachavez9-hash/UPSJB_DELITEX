<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    // Listado de reportes con filtro por tipo y por usuario
    public function index(Request $request)
    {
        $reports = Report::with('user')
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            ->when($request->user_id, fn ($query, $userId) => $query->where('user_id', $userId))
            ->latest('generated_date')
            ->latest('id')
            ->get();

        $tipos = Report::TIPOS;
        $users = User::orderBy('name')->get();

        return view('principal.reports.index', compact('reports', 'tipos', 'users'));
    }

    // Formulario para generar un reporte
    public function create()
    {
        return view('principal.reports.create', $this->datosFormulario());
    }

    // Guardar el reporte; si el contenido llega vacío lo arma generarReporte()
    public function store(Request $request)
    {
        $report = new Report($this->validar($request));
        $this->completarContenido($report);
        $report->save();

        return to_route('reports.show', $report)->with('ok', 'Reporte generado correctamente.');
    }

    // Ver el reporte
    public function show(Report $report)
    {
        $report->load('user');

        return view('principal.reports.show', compact('report'));
    }

    // Formulario para editar
    public function edit(Report $report)
    {
        return view('principal.reports.edit', array_merge($this->datosFormulario(), compact('report')));
    }

    // Actualizar el reporte; si se borra el contenido se vuelve a generar con los datos actuales
    public function update(Request $request, Report $report)
    {
        $report->fill($this->validar($request));
        $this->completarContenido($report);
        $report->save();

        return to_route('reports.show', $report)->with('ok', 'Reporte actualizado correctamente.');
    }

    // Eliminar reporte
    public function destroy(Report $report)
    {
        $report->delete();

        return to_route('reports.index')->with('ok', 'Reporte eliminado correctamente.');
    }

    private function datosFormulario(): array
    {
        return [
            'tipos' => Report::TIPOS,
            'users' => User::with('role')->orderBy('name')->get(),
        ];
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => ['required', Rule::in(Report::TIPOS)],
            'generated_date' => 'required|date',
            'content' => 'nullable|max:5000',
        ], [], [
            'user_id' => 'usuario',
        ]);
    }

    private function completarContenido(Report $report): void
    {
        if (blank($report->content)) {
            $report->content = $report->generarReporte();
        }
    }
}
