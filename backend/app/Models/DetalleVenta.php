<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product; // Importa el modelo relacionado

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas'; // nombre real de la tabla
    protected $fillable = [
        'venta_id',
        'product_id',
        'product_name',
        'cantidad',
        'precio',
        'subtotal',
        // otros campos según tu base de datos
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
        public function producto(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }
}
