<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Procesa la compra y registra Venta + DetalleVenta
     */

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function store(Request $request)
    {
        try {
            // Valida los datos
            $validated = $request->validate([
                'items' => 'required|array',
                'total' => 'required|numeric',
            ]);

            // Crea la venta
            $venta = Venta::create([
                'total' => $validated['total'],
                // agrega aquí otros campos necesarios
            ]);

            // Opcional: Crear detalles de venta (si manejas tabla detalles)
            foreach ($validated['items'] as $item) {
                $venta->detalles()->create([
                    'producto_id' => $item['id'],
                    'cantidad' => $item['quantity'],
                    'precio' => $item['price'],
                ]);
            }

            return response()->json([
                'message' => 'Venta creada correctamente.',
                'venta' => $venta,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la venta.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function checkout(Request $request)
    {
        // Validación
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Crear la venta
            $venta = Venta::create([
                'user_id' => auth()->id(),
                'total' => $request->total,
            ]);
            // Registrar cada detalle de venta
            foreach ($request->items as $item) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'cantidad' => $item['quantity'],
                    'precio' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => '✅ Compra realizada con éxito.',
                'venta_id' => $venta->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return response()->json([
                'message' => '❌ Error al procesar la compra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getComprasUsuario()
    {
        try {
            $user = Auth::user();

            $ventas = Venta::where('user_id', $user->id)
                ->with('detalles.producto') // carga detalles y producto en cada detalle
                ->orderBy('created_at', 'desc')
                ->get();

            $response = $ventas->map(function ($venta) {
                return [
                    'id' => $venta->id,
                    'fecha' => $venta->created_at ? $venta->created_at->toDateTimeString() : null,
                    'total' => $venta->total,
                    'items' => $venta->detalles->map(function ($detalle) {
                        return [
                            'id' => $detalle->producto ? $detalle->producto->id : null,
                            'name' => $detalle->product_name ?? 'Producto no disponible',
                            'quantity' => $detalle->cantidad,
                            'price' => $detalle->precio
                        ];
                    })
                ];
            });

            return response()->json($response, 200);
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['error' => 'Error al obtener las compras del usuario'], 500);
        }
    }

    public function getAllVentasAdmin()
    {
        $ventas = Venta::with(['detalles.producto', 'usuario']) // Cargar productos y usuario que hizo la compra
            ->orderBy('created_at', 'desc')
            ->get();

        $response = $ventas->map(function ($venta) {
            return [
                'id' => $venta->id,
                'fecha' => $venta->created_at ? $venta->created_at->toDateTimeString() : null,
                'total' => $venta->total,
                'user_name' => $venta->usuario->name ?? 'Desconocido', // Ajusta según tu columna de nombre
                'items' => $venta->detalles->map(function ($detalle) {
                    return [
                        'id' => $detalle->producto ? $detalle->producto->id : null,
                        'name' => $detalle->producto->name ?? 'Producto no disponible',
                        'quantity' => $detalle->cantidad,
                        'price' => $detalle->precio
                    ];
                })
            ];
        });

        return response()->json($response);
    }
}