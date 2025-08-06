<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // Listar compras con detalles para frontend
    public function index()
    {
        $purchases = Purchase::with('provider', 'items.product')->get();

        $formatted = [];

        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $item) {
                $formatted[] = [
                    'id' => $purchase->id,
                    'provider_id' => $purchase->provider_id,
                    'provider_name' => $purchase->provider->name,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $purchase->total,
                    'created_at' => $purchase->created_at,
                ];
            }
        }

        return response()->json($formatted);
    }

    // Crear compra + item + actualizar stock
    public function store(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);

            $purchase = Purchase::create([
                'provider_id' => $request->provider_id,
                'purchase_date' => now(),
                'total' => $request->quantity * $request->unit_price,
            ]);

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
            ]);

            // Actualizar stock
            $product->increment('stock', $request->quantity);

            DB::commit();

            return response()->json([
                'message' => 'Compra registrada correctamente',
                'id' => $purchase->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar la compra'], 500);
        }
    }

    // Actualizar compra + item + stock
    public function update(Request $request, $id)
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::findOrFail($id);
            $purchaseItem = $purchase->items()->first();

            if (!$purchaseItem) {
                return response()->json(['error' => 'Ítem de compra no encontrado'], 404);
            }

            // Obtener producto anterior y nuevo para actualizar stock correctamente
            $oldProduct = Product::findOrFail($purchaseItem->product_id);
            $newProduct = Product::findOrFail($request->product_id);

            // Ajustar stock: revertir cantidad anterior
            $oldProduct->decrement('stock', $purchaseItem->quantity);

            // Actualizar datos compra
            $purchase->update([
                'provider_id' => $request->provider_id,
                'purchase_date' => now(),
                'total' => $request->quantity * $request->unit_price,
            ]);

            // Actualizar ítem compra
            $purchaseItem->update([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
            ]);

            // Incrementar stock del nuevo producto
            $newProduct->increment('stock', $request->quantity);

            DB::commit();

            return response()->json(['message' => 'Compra actualizada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar la compra'], 500);
        }
    }

    // Eliminar compra + ítems + actualizar stock
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $purchase = Purchase::findOrFail($id);

            foreach ($purchase->items as $item) {
                $product = Product::findOrFail($item->product_id);
                // Reducir stock de acuerdo a la cantidad de ítem que se elimina
                $product->decrement('stock', $item->quantity);
            }

            // Eliminar ítems de compra
            $purchase->items()->delete();

            // Eliminar compra
            $purchase->delete();

            DB::commit();

            return response()->json(['message' => 'Compra eliminada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la compra'], 500);
        }
    }
}