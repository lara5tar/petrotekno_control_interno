<?php

namespace App\Http\Controllers;

use App\Services\AlertasMantenimientoService;
use Illuminate\Http\Request;

class TestAlertasController extends Controller
{
    public function testNavbar()
    {
        try {
            $resumenCompleto = AlertasMantenimientoService::obtenerResumenAlertas();
            $resumen = $resumenCompleto['resumen'] ?? [];
            
            $alertasCount = $resumen['total_alertas'] ?? 0;
            $tieneAlertasUrgentes = ($resumen['por_urgencia']['critica'] ?? 0) > 0;
            
            return view('test-navbar', compact('alertasCount', 'tieneAlertasUrgentes'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
