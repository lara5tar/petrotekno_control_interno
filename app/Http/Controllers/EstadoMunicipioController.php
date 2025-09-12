<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EstadoMunicipioController extends Controller
{
    /**
     * Obtener la lista de estados
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEstados()
    {
        $estadosPath = base_path('estados.json');
        
        if (!File::exists($estadosPath)) {
            return response()->json([], 404);
        }
        
        $estados = json_decode(File::get($estadosPath), true);
        return response()->json($estados);
    }
    
    /**
     * Obtener los municipios de un estado específico
     *
     * @param string $estado
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMunicipios($estado)
    {
        $municipiosPath = base_path('estados-municipios.json');
        
        if (!File::exists($municipiosPath)) {
            return response()->json([], 404);
        }
        
        $estadosMunicipios = json_decode(File::get($municipiosPath), true);
        
        // Buscar el estado (puede estar en mayúsculas o minúsculas)
        $municipios = [];
        foreach ($estadosMunicipios as $nombreEstado => $municipiosEstado) {
            if (strtoupper($nombreEstado) === strtoupper($estado)) {
                $municipios = $municipiosEstado;
                break;
            }
        }
        
        return response()->json($municipios);
    }
}