<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo que representa una línea de producto (artículo) dentro de un carrito.
 *
 * @property int $id
 * @property int $carrito_id
 * @property int $tienda_id
 * @property int $producto_id
 * @property int $cantidad
 * @property float $precio
 * @property-read float $importe Importe total calculado de la línea (cantidad * precio).
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CarritoItem extends Model
{
    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'carrito_id',
        'tienda_id',
        'producto_id',
        'cantidad',
        'precio',
    ];

    /**
     * Obtiene el carrito al que pertenece este artículo.
     *
     * @return BelongsTo<Carrito, $this>
     */
    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class);
    }

    /**
     * Obtiene la tienda asociada a este artículo del carrito,
     * incluyendo tiendas eliminadas (SoftDeletes) para evitar errores de lectura.
     *
     * @return BelongsTo<Tienda, $this>
     */
    public function tienda(): BelongsTo
    {
        return $this->belongsTo(Tienda::class)->withTrashed();
    }

    /**
     * Obtiene el producto asociado a este registro.
     *
     * @return BelongsTo<Producto, $this>
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Calcula el importe de la línea al vuelo (cantidad * precio) sin depender de columna física.
     *
     * @return float
     */
    public function getImporteAttribute(): float
    {
        return round($this->cantidad * $this->precio, 2);
    }
}
