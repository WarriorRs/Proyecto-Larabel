<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index()
    {
        return Provider::all();
    }

    public function show($id)
    {
        $provider = Provider::find($id);

        if (!$provider) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        return $provider;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $provider = Provider::create($validated);

        return response()->json($provider, 201);
    }

    public function update(Request $request, $id)
    {
        $provider = Provider::find($id);

        if (!$provider) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $provider->update($request->only(['name', 'phone', 'address']));

        return response()->json($provider);
    }

    public function destroy($id)
    {
        $provider = Provider::find($id);

        if (!$provider) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $provider->delete();

        return response()->json(['message' => 'Proveedor eliminado']);
    }
}