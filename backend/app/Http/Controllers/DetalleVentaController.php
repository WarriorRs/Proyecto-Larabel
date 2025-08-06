<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use Illuminate\Http\Request;

class DetalleVentaController extends Controller
{
    public function store(Request $request)
    {
        $detalleVenta = DetalleVenta::create($request->all());
        return response()->json($detalleVenta, 201);
    }

    public function index()
    {
        $detalles = DetalleVenta::all();
        return response()->json($detalles);
    }

    // Agregar otros métodos show, update, destroy si necesitas
}
